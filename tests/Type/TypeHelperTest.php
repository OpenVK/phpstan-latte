<?php

declare(strict_types=1);

namespace Efabrica\PHPStanLatte\Tests\Type;

use Efabrica\PHPStanLatte\Type\TypeHelper;
use PHPStan\Reflection\Native\NativeParameterReflection;
use PHPStan\Reflection\PassedByReference;
use PHPStan\Testing\PHPStanTestCase;
use PHPStan\Type\ArrayType;
use PHPStan\Type\CallableType;
use PHPStan\Type\IntegerType;
use PHPStan\Type\IterableType;
use PHPStan\Type\MixedType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\StringType;

final class TypeHelperTest extends PHPStanTestCase
{
    public function testExpandsBareIterables(): void
    {
        self::assertSame('array<mixed, mixed>', (string)TypeHelper::toPhpDocNode(new ArrayType(new MixedType(), new MixedType())));
        self::assertSame('iterable<mixed, mixed>', (string)TypeHelper::toPhpDocNode(new IterableType(new MixedType(), new MixedType())));
        self::assertSame('Generator<mixed, mixed, mixed, mixed>', (string)TypeHelper::toPhpDocNode(new ObjectType('Generator')));
    }

    public function testKeepsTypedGenericsUntouched(): void
    {
        self::assertSame('array<int, string>', (string)TypeHelper::toPhpDocNode(new ArrayType(new IntegerType(), new StringType())));
        self::assertSame('array<string>', (string)TypeHelper::toPhpDocNode(new ArrayType(new MixedType(), new StringType())));
    }

    public function testExpandsBareIterablesInsideTypedGenerics(): void
    {
        $bare = new ArrayType(new MixedType(), new MixedType());
        self::assertSame('array<int, array<mixed, mixed>>', (string)TypeHelper::toPhpDocNode(new ArrayType(new IntegerType(), $bare)));
    }

    public function testKeepsCallableParameterGenericsUntouched(): void
    {
        $callable = new CallableType([
            new NativeParameterReflection('', false, new ArrayType(new IntegerType(), new StringType()), PassedByReference::createNo(), false, null),
        ], new StringType());
        self::assertSame('callable(array<int, string>): string', (string)TypeHelper::toPhpDocNode($callable));
    }

    public function testExpandsBareIterablesInCallableParameters(): void
    {
        $callable = new CallableType([
            new NativeParameterReflection('', false, new ArrayType(new MixedType(), new MixedType()), PassedByReference::createNo(), false, null),
        ], new StringType());
        self::assertSame('callable(array<mixed, mixed>): string', (string)TypeHelper::toPhpDocNode($callable));
    }
}
