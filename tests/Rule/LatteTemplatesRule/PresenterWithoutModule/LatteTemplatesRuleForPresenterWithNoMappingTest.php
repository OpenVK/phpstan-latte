<?php

declare(strict_types=1);

namespace Efabrica\PHPStanLatte\Tests\Rule\LatteTemplatesRule\PresenterWithoutModule;

use Efabrica\PHPStanLatte\Tests\DependencyVersion;
use Efabrica\PHPStanLatte\Tests\Rule\LatteTemplatesRule\LatteTemplatesRuleTest;
use Efabrica\PHPStanLatte\Tests\Rule\LatteTemplatesRule\PresenterWithoutModule\Fixtures\LinksPresenter;

class LatteTemplatesRuleForPresenterWithNoMappingTest extends LatteTemplatesRuleTest
{
    protected static function additionalConfigFiles(): array
    {
        return [
            __DIR__ . '/../../../../rules.neon',
            __DIR__ . '/../../../config.neon',
            __DIR__ . '/config.neon',
            __DIR__ . '/strict.neon',
        ];
    }

    public function testLinks(): void
    {
        // Without mapping only handle links are created, no other errors should be found
        $expectedErrors = [
            [
                'Parameter #1 $id of method ' . LinksPresenter::class . '::handleDelete() expects string, null given.',
                97,
                'default.latte',
            ],
            [
                'Method ' . LinksPresenter::class . '::handleDelete() invoked with 2 parameters, 1 required.',
                98,
                'default.latte',
            ],
            [
                'Parameter #1 $destination of method Nette\Application\UI\Component::link() expects string, Latte\Runtime\Html|string|false given.',
                103,
                'default.latte',
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
        ];
        if (DependencyVersion::phpstanAtLeast23() && !$this->usesSeparatedPhpStanCommand()) {
            // PHPStan 2.3 dead-code and repeated-assignment checks on generated template code;
            // the separated phpstan command does not report them.
            $expectedErrors[] = [
                'Variable $ʟ_tmp is assigned value array{aa: 1, bb: 1} but it already has that value.',
                7,
                'default.latte',
            ];
            $expectedErrors[] = [
                'Variable $ʟ_tmp is assigned value array{aa: 1, bb: 1} but it already has that value.',
                101,
                'default.latte',
            ];
            $expectedErrors[] = [
                'Value assigned to variable $ʟ_fi is never read.',
                102,
                'default.latte',
            ];
        }

        $this->analyse([__DIR__ . '/Fixtures/LinksPresenter.php'], $expectedErrors);
    }

    protected function usesSeparatedPhpStanCommand(): bool
    {
        return false;
    }
}
