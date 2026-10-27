<?php

declare(strict_types=1);

namespace Efabrica\PHPStanLatte\Tests\Rule\LatteTemplatesRule\EngineBootstrap;

abstract class ParentFirstClassCallableFilters
{
    /**
     * @return static
     */
    public function inheritedFilter(string $input)
    {
        return $this;
    }
}
