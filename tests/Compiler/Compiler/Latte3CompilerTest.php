<?php

declare(strict_types=1);

namespace Efabrica\PHPStanLatte\Tests\Compiler\Compiler;

use Efabrica\PHPStanLatte\Compiler\Compiler\Latte3Compiler;
use Efabrica\PHPStanLatte\Compiler\LatteVersion;
use Latte\Engine;
use Latte\Feature;
use PHPUnit\Framework\TestCase;

final class Latte3CompilerTest extends TestCase
{
    public function testFeatureStateChangesCacheKey(): void
    {
        if (!LatteVersion::isLatte31()) {
            $this->markTestSkipped('Engine features are a Latte 3.1 feature.');
        }

        $engine = new Engine();
        $engineWithFeature = new Engine();
        $engineWithFeature->setFeature(Feature::ScopedLoopVariables);

        self::assertNotSame(
            (new Latte3Compiler($engine))->getCacheKey(),
            (new Latte3Compiler($engineWithFeature))->getCacheKey()
        );
    }

    public function testDoesNotRewriteStringLiteralsInPosComments(): void
    {
        if (!LatteVersion::isLatte3()) {
            $this->markTestSkipped('Latte 3 compiler only.');
        }

        $phpContent = (new Latte3Compiler())->compile('{php \PHPStan\dumpType(\'/* pos 1:2 */\')}', null);

        self::assertStringContainsString("'/* pos 1:2 */'", $phpContent);
    }
}
