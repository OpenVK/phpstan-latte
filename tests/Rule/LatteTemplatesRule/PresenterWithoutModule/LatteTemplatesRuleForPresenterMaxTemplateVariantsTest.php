<?php

declare(strict_types=1);

namespace Efabrica\PHPStanLatte\Tests\Rule\LatteTemplatesRule\PresenterWithoutModule;

use Efabrica\PHPStanLatte\Tests\Rule\LatteTemplatesRule\LatteTemplatesRuleTest;

final class LatteTemplatesRuleForPresenterMaxTemplateVariantsTest extends LatteTemplatesRuleTest
{
    protected static function additionalConfigFiles(): array
    {
        return [
            __DIR__ . '/../../../../rules.neon',
            __DIR__ . '/../../../config.neon',
            __DIR__ . '/config.neon',
            __DIR__ . '/mapping.neon',
            __DIR__ . '/config-maxTemplateVariants.neon',
        ];
    }

    public function testRecursion(): void
    {
        $this->analyse([__DIR__ . '/Fixtures/RecursionPresenter.php'], [
            [
                'Skipped 2 variant(s) of template ' . __DIR__ . '/Fixtures/templates/@layout.latte because of latte.maxTemplateVariants.',
                -1,
                '@layout.latte',
            ],
            [
                'Skipped 1 variant(s) of template ' . __DIR__ . '/Fixtures/templates/Recursion/indirectRecursion.latte because of latte.maxTemplateVariants.',
                -1,
                'indirectRecursion.latte',
            ],
            [
                'Skipped 1 variant(s) of template ' . __DIR__ . '/Fixtures/templates/Recursion/recursion.latte because of latte.maxTemplateVariants.',
                -1,
                'recursion.latte',
            ],
            [
                'Dumped type: 9',
                1,
                '@indirectRecursionB.latte',
            ],
            [
                'Dumped type: 7',
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
                'Dumped type: 8',
                1,
                'indirectRecursion.latte',
            ],
            [
                'Comparison operation ">" between 10 and 0 is always true.',
                2,
                'indirectRecursion.latte',
            ],
            [
                'Comparison operation ">" between 8 and 0 is always true.',
                2,
                'indirectRecursion.latte',
            ],
            [
                'Dumped type: 10',
                1,
                'recursion.latte',
            ],
            [
                'Dumped type: 9',
                1,
                'recursion.latte',
            ],
            [
                'Comparison operation ">" between 10 and 0 is always true.',
                2,
                'recursion.latte',
            ],
            [
                'Comparison operation ">" between 9 and 0 is always true.',
                2,
                'recursion.latte',
            ],
        ]);
    }
}
