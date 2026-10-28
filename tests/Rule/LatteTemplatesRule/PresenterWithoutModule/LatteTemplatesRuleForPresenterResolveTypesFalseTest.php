<?php

declare(strict_types=1);

namespace Efabrica\PHPStanLatte\Tests\Rule\LatteTemplatesRule\PresenterWithoutModule;

use Efabrica\PHPStanLatte\Tests\Rule\LatteTemplatesRule\LatteTemplatesRuleTest;

final class LatteTemplatesRuleForPresenterResolveTypesFalseTest extends LatteTemplatesRuleTest
{
    protected static function additionalConfigFiles(): array
    {
        return [
            __DIR__ . '/../../../../rules.neon',
            __DIR__ . '/../../../config.neon',
            __DIR__ . '/config.neon',
            __DIR__ . '/mapping.neon',
            __DIR__ . '/config-resolveTypesFalse.neon',
        ];
    }

    public function testFiltersAndSnippetsStillWork(): void
    {
        $this->analyse([__DIR__ . '/Fixtures/ResolveTypesPresenter.php'], [
            [
                'Parameter #1 of closure expects string, int given.',
                5,
                'default.latte',
            ],
        ]);
    }
}
