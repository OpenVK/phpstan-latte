<?php

declare(strict_types=1);

namespace Efabrica\PHPStanLatte\Tests\Rule\LatteTemplatesRule\PresenterWithoutModule\Source;

final class CustomTemplate
{
    public function addFilter(string $name, callable $filter): void
    {
    }

    public function add(string $name, $value): void
    {
    }

    public function setFile(string $file): void
    {
    }
}
