<?php

declare(strict_types=1);

namespace Efabrica\PHPStanLatte\Tests\Rule\LatteTemplatesRule\PresenterWithoutModule\Fixtures;

use Efabrica\PHPStanLatte\Tests\Rule\LatteTemplatesRule\PresenterWithoutModule\Source\CustomTemplate;
use Nette\Application\UI\Presenter;

final class TemplateTypesPresenter extends Presenter
{
    public function actionDefault(): void
    {
        $this->template->title = 'title';
        $customTemplate = new CustomTemplate();
        $customTemplate->setFile(__DIR__ . '/templates/TemplateTypes/other.latte');
    }
}
