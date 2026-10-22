<?php

declare(strict_types=1);

namespace Efabrica\PHPStanLatte\Tests\Rule\LatteTemplatesRule\EngineBootstrap;

abstract class ParentFirstClassCallableFilters
{
    public function inheritedFilter(string $input): static
    {
        return $this;
    }
}
