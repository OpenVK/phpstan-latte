<?php

declare(strict_types=1);

namespace Efabrica\PHPStanLatte\Tests\Rule\LatteTemplatesRule\PresenterWithoutModule;

use Efabrica\PHPStanLatte\Tests\Rule\LatteTemplatesRule\LatteTemplatesRuleTest;

final class LatteTemplatesRuleForPresenterBreakIncludeCyclesTest extends LatteTemplatesRuleTest
{
    protected static function additionalConfigFiles(): array
    {
        return [
            __DIR__ . '/../../../../rules.neon',
            __DIR__ . '/../../../config.neon',
            __DIR__ . '/config.neon',
            __DIR__ . '/mapping.neon',
            __DIR__ . '/config-breakIncludeCycles.neon',
        ];
    }

    public function testRecursion(): void
    {
        $this->analyse([__DIR__ . '/Fixtures/RecursionPresenter.php'], [
            [
                'Dumped type: 9',
                1,
                '@indirectRecursionB.latte',
            ],
            [
                'Undefined variable: $title',
                7,
                '@layout.latte',
            ],
            [
                'Undefined variable: $title',
                7,
                '@layout.latte',
            ],
            [
                'Undefined variable: $title',
                7,
                '@layout.latte',
            ],
            [
                'Undefined variable: $title',
                7,
                '@layout.latte',
            ],
            [
                'Undefined variable: $neverDefined',
                10,
                '@layout.latte',
            ],
            [
                'Undefined variable: $neverDefined',
                10,
                '@layout.latte',
            ],
            [
                'Undefined variable: $neverDefined',
                10,
                '@layout.latte',
            ],
            [
                'Undefined variable: $neverDefined',
                10,
                '@layout.latte',
            ],
            [
                'Dumped type: 10',
                1,
                'indirectRecursion.latte',
            ],
            [
                'Comparison operation ">" between 10 and 0 is always true.',
                2,
                'indirectRecursion.latte',
            ],
            [
                'Dumped type: 10',
                1,
                'recursion.latte',
            ],
            [
                'Comparison operation ">" between 10 and 0 is always true.',
                2,
                'recursion.latte',
            ],
        ]);
    }
}
