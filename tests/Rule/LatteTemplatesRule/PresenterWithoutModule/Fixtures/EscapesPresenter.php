<?php

declare(strict_types=1);

namespace Efabrica\PHPStanLatte\Tests\Rule\LatteTemplatesRule\PresenterWithoutModule\Fixtures;

use Nette\Application\UI\Presenter;
use stdClass;

final class EscapesPresenter extends Presenter
{
    public function actionDefault(): void
    {
        $this->template->value = new stdClass();
    }
}
