<?php

declare(strict_types=1);

namespace Efabrica\PHPStanLatte\Tests\Resolver\LayoutResolver;

use Efabrica\PHPStanLatte\Resolver\LayoutResolver\LayoutPathResolver;
use PHPUnit\Framework\TestCase;
use function realpath;

final class LayoutPathResolverTest extends TestCase
{
    private LayoutPathResolver $resolver;

    protected function setUp(): void
    {
        $this->resolver = new LayoutPathResolver(true);
    }

    public function testResolvesQuotedLayout(): void
    {
        self::assertSame(
            realpath(__DIR__ . '/Fixtures/layout.latte'),
            $this->resolver->resolve(__DIR__ . '/Fixtures/with-layout.latte')
        );
    }

    public function testResolvesExtends(): void
    {
        self::assertSame(
            realpath(__DIR__ . '/Fixtures/layout.latte'),
            $this->resolver->resolve(__DIR__ . '/Fixtures/with-extends.latte')
        );
    }

    public function testLayoutNoneDisablesLayout(): void
    {
        self::assertNull($this->resolver->resolve(__DIR__ . '/Fixtures/with-none.latte'));
    }

    public function testMissingExplicitLayoutIsReported(): void
    {
        self::assertSame(
            __DIR__ . '/Fixtures/missing.latte',
            $this->resolver->resolve(__DIR__ . '/Fixtures/with-missing.latte')
        );
    }

    public function testDynamicLayoutFallsBackToConventionalLayout(): void
    {
        self::assertSame(
            realpath(__DIR__ . '/Fixtures/@layout.latte'),
            $this->resolver->resolve(__DIR__ . '/Fixtures/with-dynamic.latte')
        );
    }

    public function testAutoLayoutUsesConventionalLayout(): void
    {
        self::assertSame(
            realpath(__DIR__ . '/Fixtures/@layout.latte'),
            $this->resolver->resolve(__DIR__ . '/Fixtures/with-auto.latte')
        );
    }

    public function testDisabledFeatureDoesNotResolve(): void
    {
        self::assertNull((new LayoutPathResolver(false))->resolve(__DIR__ . '/Fixtures/with-layout.latte'));
    }
}
