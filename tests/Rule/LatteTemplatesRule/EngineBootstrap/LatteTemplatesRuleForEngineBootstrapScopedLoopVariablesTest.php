<?php

declare(strict_types=1);

namespace Efabrica\PHPStanLatte\Tests\Rule\LatteTemplatesRule\EngineBootstrap;

use Efabrica\PHPStanLatte\Compiler\LatteVersion;
use Efabrica\PHPStanLatte\Tests\Rule\LatteTemplatesRule\LatteTemplatesRuleTest;

final class LatteTemplatesRuleForEngineBootstrapScopedLoopVariablesTest extends LatteTemplatesRuleTest
{
    protected static function additionalConfigFiles(): array
    {
        return [
            __DIR__ . '/../../../../rules.neon',
            __DIR__ . '/../../../config.neon',
            __DIR__ . '/config-scopedLoopVariables.neon',
        ];
    }

    public function testScopedLoopVariables(): void
    {
        if (!LatteVersion::isLatte31()) {
            $this->markTestSkipped('ScopedLoopVariables is a Latte 3.1 feature.');
        }
        $this->analyse([__DIR__ . '/Fixtures/ScopedLoopVariablesPresenter.php'], [
            [
                "Dumped type: 'before'",
                5,
                'default.latte',
            ],
        ]);
    }
}
