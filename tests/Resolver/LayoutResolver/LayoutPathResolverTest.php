<?php

declare(strict_types=1);

namespace Efabrica\PHPStanLatte\Tests\Resolver\LayoutResolver;

use Efabrica\PHPStanLatte\Resolver\LayoutResolver\LayoutPathResolver;
use PHPUnit\Framework\TestCase;
use function file_put_contents;
use function mkdir;
use function realpath;
use function rmdir;
use function sys_get_temp_dir;
use function uniqid;
use function unlink;

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

    public function testResolvesBareLayout(): void
    {
        self::assertSame(
            realpath(__DIR__ . '/Fixtures/layout.latte'),
            $this->resolver->resolve(__DIR__ . '/Fixtures/with-bare.latte')
        );
    }

    public function testResolvesExtensionlessBareLayout(): void
    {
        self::assertSame(
            realpath(__DIR__ . '/Fixtures/main'),
            $this->resolver->resolve(__DIR__ . '/Fixtures/with-extensionless.latte')
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

    public function testConcatenatedLayoutIsDynamic(): void
    {
        self::assertSame(
            realpath(__DIR__ . '/Fixtures/@layout.latte'),
            $this->resolver->resolve(__DIR__ . '/Fixtures/with-concat.latte')
        );
    }

    public function testInterpolatedLayoutIsDynamic(): void
    {
        self::assertSame(
            realpath(__DIR__ . '/Fixtures/@layout.latte'),
            $this->resolver->resolve(__DIR__ . '/Fixtures/with-interpolation.latte')
        );
    }

    public function testDoubleQuotedLayoutIsResolved(): void
    {
        self::assertSame(
            realpath(__DIR__ . '/Fixtures/layout.latte'),
            $this->resolver->resolve(__DIR__ . '/Fixtures/with-double-quoted.latte')
        );
    }

    public function testQuotedNoneIsALiteralPath(): void
    {
        self::assertSame(
            __DIR__ . '/Fixtures/none',
            $this->resolver->resolve(__DIR__ . '/Fixtures/with-quoted-none.latte')
        );
    }

    public function testAbsoluteLayoutPathIsResolved(): void
    {
        $dir = sys_get_temp_dir() . '/phpstan-latte-layout-' . uniqid('', true);
        mkdir($dir);
        file_put_contents($dir . '/layout.latte', '');
        file_put_contents($dir . '/template.latte', "{layout '" . $dir . "/layout.latte'}");
        try {
            self::assertSame(
                realpath($dir . '/layout.latte'),
                $this->resolver->resolve($dir . '/template.latte')
            );
        } finally {
            unlink($dir . '/layout.latte');
            unlink($dir . '/template.latte');
            rmdir($dir);
        }
    }
}
