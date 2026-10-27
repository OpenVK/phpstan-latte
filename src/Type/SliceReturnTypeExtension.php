<?php

declare(strict_types=1);

namespace Efabrica\PHPStanLatte\Type;

use PhpParser\Node\Expr\StaticCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\ArrayType;
use PHPStan\Type\DynamicStaticMethodReturnTypeExtension;
use PHPStan\Type\MixedType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use PHPStan\Type\UnionType;

/**
 * Latte's slice() returns a plain value for strings and arrays, and a Generator only for
 * other iterables. The native string|array|Generator return type cannot express that, so
 * narrow it from the first argument.
 */
final class SliceReturnTypeExtension implements DynamicStaticMethodReturnTypeExtension
{
    /** @var class-string */
    private string $className;

    /**
     * @param class-string $className
     */
    public function __construct(string $className)
    {
        $this->className = $className;
    }

    public function getClass(): string
    {
        return $this->className;
    }

    public function isStaticMethodSupported(MethodReflection $methodReflection): bool
    {
        return $methodReflection->getName() === 'slice';
    }

    public function getTypeFromStaticMethodCall(MethodReflection $methodReflection, StaticCall $methodCall, Scope $scope): ?Type
    {
        $args = $methodCall->getArgs();
        if (!isset($args[0])) {
            return null;
        }

        return $this->resolve($scope->getType($args[0]->value));
    }

    private function resolve(Type $type): ?Type
    {
        if ($type instanceof UnionType) {
            $types = [];
            foreach ($type->getTypes() as $innerType) {
                // null is rejected by slice() itself; drop it to avoid the Generator branch
                if ($innerType->isNull()->yes()) {
                    continue;
                }
                $resolved = $this->resolve($innerType);
                if ($resolved === null) {
                    return null;
                }
                $types[] = $resolved;
            }
            return $types === [] ? null : TypeCombinator::union(...$types);
        }

        if ($type->isString()->yes()) {
            return new StringType();
        }
        if ($type->isArray()->yes()) {
            return new ArrayType(new MixedType(), $type->getIterableValueType());
        }
        if ($type->isIterable()->yes()) {
            return new ObjectType('Generator');
        }

        return null;
    }
}
