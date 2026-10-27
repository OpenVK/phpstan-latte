<?php

declare(strict_types=1);

namespace Efabrica\PHPStanLatte\Tests\Rule\LatteTemplatesRule\PresenterWithoutModule\Fixtures;

use Nette\Application\UI\Presenter;

final class MissingLayoutPresenter extends Presenter
{
    public function actionDefault(): void
    {
        $this->template->title = 'title';
    }
}
