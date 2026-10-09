<?php

declare(strict_types=1);

namespace Efabrica\PHPStanLatte\Tests;

use Nette\Bridges\FormsLatte\Runtime;
use PHPStan\Rules\DeadCode\UnusedVariableRule;
use function class_exists;
use function method_exists;

final class DependencyVersion
{
    /**
     * PHPStan 2.3 added UnusedVariableRule, which reports extra dead-code errors
     * ("never read", "only flows into values that are never used", ...) on generated template code.
     * Composer\InstalledVersions cannot be used here: inside RuleTestCase the PHPStan phar ships
     * its own Composer\InstalledVersions which does not know the phpstan/phpstan package version.
     */
    public static function phpstanAtLeast23(): bool
    {
        return class_exists(UnusedVariableRule::class);
    }

    /**
     * nette/forms 3.3 removed FormsLatte\Runtime::item() in favor of Runtime::get().
     */
    public static function netteFormsAtLeast33(): bool
    {
        return !method_exists(Runtime::class, 'item');
    }
}
