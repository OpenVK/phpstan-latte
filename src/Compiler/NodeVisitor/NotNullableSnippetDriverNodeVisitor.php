<?php

declare(strict_types=1);

namespace Efabrica\PHPStanLatte\Compiler\NodeVisitor;

use Efabrica\PHPStanLatte\Compiler\NodeVisitor\Behavior\ExprTypeNodeVisitorBehavior;
use Efabrica\PHPStanLatte\Compiler\NodeVisitor\Behavior\ExprTypeNodeVisitorInterface;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\NodeVisitorAbstract;
use PHPStan\Type\ObjectType;
use function in_array;

final class NotNullableSnippetDriverNodeVisitor extends NodeVisitorAbstract implements ExprTypeNodeVisitorInterface
{
    use ExprTypeNodeVisitorBehavior;

    public function enterNode(Node $node): ?Node
    {
        if (!$node instanceof NullsafeMethodCall) {
            return null;
        }

        $callerType = $this->getType($node->var);
        $isSnippetDriver = $callerType !== null
            ? (new ObjectType('Nette\Bridges\ApplicationLatte\SnippetDriver'))->isSuperTypeOf($callerType)->yes()
                || (new ObjectType('Nette\Bridges\ApplicationLatte\SnippetRuntime'))->isSuperTypeOf($callerType)->yes()
            : $this->isSnippetDriverProperty($node->var);

        if ($isSnippetDriver) {
            $methodCall = new MethodCall($node->var, $node->name, $node->args);
            $methodCall->setAttributes($node->getAttributes());
            return $methodCall;
        }

        return null;
    }

    /**
     * Fallback for the resolveTypes=false pass, when no inferred types are available.
     */
    private function isSnippetDriverProperty(Expr $expr): bool
    {
        return $expr instanceof PropertyFetch
            && $expr->name instanceof Identifier
            && in_array($expr->name->name, ['snippetDriver', 'snippetRuntime'], true)
            && $expr->var instanceof PropertyFetch
            && $expr->var->name instanceof Identifier
            && $expr->var->name->name === 'global'
            && $expr->var->var instanceof Variable
            && $expr->var->var->name === 'this';
    }
}
