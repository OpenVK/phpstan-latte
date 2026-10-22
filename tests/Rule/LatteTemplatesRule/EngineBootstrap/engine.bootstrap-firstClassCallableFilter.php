<?php

declare(strict_types=1);

namespace Efabrica\PHPStanLatte\Tests\Rule\LatteTemplatesRule\EngineBootstrap;

use Latte\Engine;
use Stringable;

require_once __DIR__ . '/Source/ParentFirstClassCallableFilters.php';

final class FirstClassCallableFilters extends ParentFirstClassCallableFilters implements Stringable
{
    public function objectFilter(string $input): string
    {
        return $input;
    }

    public function __toString(): string
    {
        return $this->objectFilter('filter');
    }
}

$engine = new Engine();
$engine->addFilter('objectFilterFirstClassCallable', (new FirstClassCallableFilters())->objectFilter(...));
$engine->addFilter('inheritedFilterFirstClassCallable', (new FirstClassCallableFilters())->inheritedFilter(...));
return $engine;
