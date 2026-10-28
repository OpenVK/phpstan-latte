<?php

declare(strict_types=1);

namespace Efabrica\PHPStanLatte\Tests\Rule\LatteTemplatesRule\EngineBootstrap;

use Latte\Engine;
use Latte\Feature;

$engine = new Engine();
$engine->setFeature(Feature::ScopedLoopVariables);
return $engine;
