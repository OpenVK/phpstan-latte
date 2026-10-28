<?php

declare(strict_types=1);

namespace Efabrica\PHPStanLatte\Tests\Rule\LatteTemplatesRule\PresenterWithoutModule;

use Efabrica\PHPStanLatte\Tests\Rule\LatteTemplatesRule\LatteTemplatesRuleTest;

final class LatteTemplatesRuleForPresenterTemplateTypesTest extends LatteTemplatesRuleTest
{
    protected static function additionalConfigFiles(): array
    {
        return [
            __DIR__ . '/../../../../rules.neon',
            __DIR__ . '/../../../config.neon',
            __DIR__ . '/config.neon',
            __DIR__ . '/mapping.neon',
            __DIR__ . '/config-templateTypes.neon',
        ];
    }

    public function testCustomTemplateIsAnalysed(): void
    {
        $this->analyse([__DIR__ . '/Fixtures/TemplateTypesPresenter.php'], [
            [
                'Skipped 1 variant(s) of template ' . __DIR__ . '/Fixtures/templates/TemplateTypes/other.latte because of latte.maxTemplateVariants.',
                -1,
                'other.latte',
            ],
            [
                'Undefined variable: $undefinedVariable',
                2,
                'other.latte',
            ],
        ]);
    }
}
