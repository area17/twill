<?php

namespace App\Http\Controllers\Twill;

use A17\Twill\Models\Contracts\TwillModelContract;
use A17\Twill\Services\Forms\Fields\Browser;
use A17\Twill\Services\Forms\Form;
use A17\Twill\Http\Controllers\Admin\NestedModuleController as BaseModuleController;
use App\Models\Page;

class MenuLinkController extends BaseModuleController
{
    protected $moduleName = 'menuLinks';
    protected $showOnlyParentItemsInBrowsers = true;
    protected $nestedItemsDepth = 1;

    protected function setUpController(): void
    {
        $this->disablePermalink();
        $this->enableReorder();
    }

    public function getForm(TwillModelContract $model): Form
    {
        $form = parent::getForm($model);

        $form->add(Browser::make()->name('page')->modules([Page::class]));

        return $form;
    }
}
