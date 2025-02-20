<?php

namespace App\Http\Controllers\Twill;

use A17\Twill\Http\Controllers\Admin\ModuleController as BaseModuleController;
use A17\Twill\Models\Contracts\TwillModelContract;
use A17\Twill\Services\Forms\Fields\BlockEditor;
use A17\Twill\Services\Forms\Fields\Input;
use A17\Twill\Services\Forms\Fields\Repeater;
use A17\Twill\Services\Forms\Form;
use A17\Twill\Services\Forms\InlineRepeater;
use App\Models\Link;
use App\Models\Partner;

class ProjectController extends BaseModuleController
{
    protected function setUpController(): void
    {
        $this->setModuleName('projects');
    }

    public function getForm(TwillModelContract $model): Form
    {
        return Form::make([
            Input::make()
                ->translatable()
                ->name('description'),
            // Inline repeater that can select existing entries.
            InlineRepeater::make()
                ->label('Partners')
                ->name('project_partner')
                ->triggerText('Add partner') // Can be omitted as it generates this.
                ->selectTriggerText('Select partner') // Can be omitted as it generates this.
                ->allowBrowser()
                ->relation(Partner::class)
                ->fields([
                    Input::make()
                        ->name('title')
                        ->translatable(),
                    Input::make()
                        ->name('role')
                        ->translatable()
                        ->required(),
                ]),
            Repeater::make()->type('comment'), // Regular repeater using a view.
            // Regular repeater for creating items without a managed model.
            InlineRepeater::make()
                ->name('links')
                ->fields([
                    Input::make()
                        ->name('title'),
                    Input::make()
                        ->name('url')
                ]),

            BlockEditor::make()
        ]);
    }
}
