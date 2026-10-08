<?php

declare(strict_types=1);

namespace Efabrica\PHPStanLatte\Type;

use InvalidArgumentException;
use LogicException;
use PHPStan\PhpDocParser\Ast\AbstractNodeVisitor;
use PHPStan\PhpDocParser\Ast\Node;
use PHPStan\PhpDocParser\Ast\NodeTraverser;
use PHPStan\PhpDocParser\Ast\Type\GenericTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\PhpDocParser\Printer\Printer;
use PHPStan\Reflection\ParametersAcceptor;
use PHPStan\Type\ErrorType;
use PHPStan\Type\Generic\TemplateType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\StaticType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeTraverser;
use function array_map;
use function count;

final class TypeHelper
{
    public static function resolveType(Type $type): Type
    {
        $type = TypeTraverser::map($type, static function (Type $type, callable $traverse): Type {
            if ($type instanceof StaticType) {
                return $traverse($type->getStaticObjectType());
            }
            return $traverse($type);
        });

        return $type;
    }

    public static function resolveTypeBounds(Type $type): Type
    {
        $type = self::resolveType($type);

        // replace unresolved template types with their bounds (T of stdClass -> stdClass)
        $type = TypeTraverser::map($type, static function (Type $type, callable $traverse): Type {
            if ($type instanceof TemplateType) {
                return $traverse($type->getBound());
            }
            return $traverse($type);
        });

        return $type;
    }

    public static function resolveTemplateType(Type $type, string $declaringClass, ?string $currentClass): Type
    {
        $currentType = new ObjectType($currentClass ?? $declaringClass);
        $declaringType = $currentType->getAncestorWithClassName($declaringClass);
        if ($declaringType === null) {
            return $type;
        }
        $declaringClassReflection = $declaringType->getClassReflection();
        if ($declaringClassReflection === null) {
            return $type;
        }
        $typeMap = $declaringClassReflection->getActiveTemplateTypeMap();
        return TypeTraverser::map($type, static function (Type $type, callable $traverse) use ($typeMap): Type {
            if ($type instanceof TemplateType) {
                $newType = $typeMap->getType($type->getName());
                if ($newType === null) {
                    return $traverse($type);
                }
                if ($newType instanceof ErrorType) {
                    return $traverse($type->getBound());
                }
                return $newType;
            }
            return $traverse($type);
        });
    }

    /**
     * @param ParametersAcceptor[] $variants
     */
    public static function getFirstParamTypeName(array $variants): ?string
    {
        if ($variants === []) {
            return null;
        }
        $params = $variants[0]->getParameters();
        if ($params === []) {
            return null;
        }
        $classNames = $params[0]->getType()->getObjectClassNames();
        return $classNames[0] ?? null;
    }

    /**
     * PHPDoc node of the type with bare iterables expanded to explicit "mixed" type arguments.
     *
     * Latte 3.1 registers its built-in filters and functions as first-class callables, so their
     * resolved closure types embed bare "array"/"iterable"/"Traversable"/"Generator" types which
     * would trigger missingType errors in the generated templates.
     */
    public static function toPhpDocNode(Type $type): TypeNode
    {
        $traverser = new NodeTraverser([
            new class extends AbstractNodeVisitor {
                public function enterNode(Node $node): ?Node
                {
                    if (!$node instanceof IdentifierTypeNode || $node->getAttribute('latteTypeExpanded')) {
                        return null;
                    }

                    $typeNames = match ($node->name) {
                        'array', 'iterable', 'Traversable', 'Iterator' => ['mixed', 'mixed'],
                        'Generator' => ['mixed', 'mixed', 'mixed', 'mixed'],
                        default => null,
                    };
                    if ($typeNames === null) {
                        return null;
                    }

                    $identifier = new IdentifierTypeNode($node->name);
                    $identifier->setAttribute('latteTypeExpanded', true);
                    return new GenericTypeNode($identifier, array_map(
                        static fn (string $typeName): IdentifierTypeNode => new IdentifierTypeNode($typeName),
                        $typeNames
                    ));
                }
            },
        ]);

        /** @var TypeNode[] $nodes */
        $nodes = $traverser->traverse([$type->toPhpDocNode()]);
        return $nodes[0];
    }

    public static function serializeType(Type $type): string
    {
        $type = TypeTraverser::map($type, static function (Type $type, callable $traverse): Type {
            if ($type instanceof StaticType) {
                return $traverse($type->getStaticObjectType());
            }
            if ($type instanceof ErrorType) {
                throw new InvalidArgumentException('Cannot serialize ErrorType');
            }
            if ($type instanceof TemplateType) {
                throw new InvalidArgumentException('Cannot serialize TemplateType');
            }
            return $traverse($type);
        });

        return (new Printer())->print($type->toPhpDocNode());
    }

    /**
     * @return mixed
     */
    public static function typeToValue(?Type $type)
    {
        if ($type === null) {
            return null;
        }

        try {
            if ($type->isConstantScalarValue()->yes()) {
                $values = $type->getConstantScalarValues();
                if (count($values) === 1) {
                    return $values[0];
                }
            }

            $constantArrays = $type->getConstantArrays();
            if (count($constantArrays) === 1 && $constantArrays[0]->getKeyTypes() === []) {
                return [];
            }
        } catch (LogicException $e) {
        }

        return null;
    }
}
