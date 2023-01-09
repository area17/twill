# Inline repeater

Inline repeaters are [Form builder](../3_modules/8_form-builder.md) only.

This field will allow you to create repeaters inline. This works for json repeaters or regular repeaters
and can both be used in block components and page forms.

While in theory these can be nested, nested inline repeaters only work on blocks not controller forms.

## Usage

### Json

When using a json repeater in a block, the setup is straightforward and all you have to do is add the
inline repeater to your block form.

```php
<?php
namespace App\View\Components\Twill\Blocks;

use A17\Twill\Services\Forms\Fields\Wysiwyg;
use A17\Twill\Services\Forms\Form;
use A17\Twill\Services\Forms\Fields\Input;
use A17\Twill\Services\Forms\InlineRepeater;use A17\Twill\View\Components\Blocks\TwillBlockComponent;
use Illuminate\Contracts\View\View;

class Example extends TwillBlockComponent
{
    public function render(): View
    {
        return view('components.twill.blocks.example');
    }
    public function getForm(): Form
    {
        return Form::make([
            InlineRepeater::make()->name('links') //[tl! focus:start]
                ->fields([
                    Input::make()->name('title'),
                    Input::make()->name('url'),
                ]) //[tl! focus:end]
        ]);
    }
}
```

On a regular controller form you have to still setup the [handleJsonRepeaters](../../2_guides/json-repeaters.md).

### Relations

When you are working with relations, you have to setup a little bit more.

Below is a full fledged example coming from the `portfolio` installable example:

```php
<?php

namespace App\Http\Controllers\Twill;

use A17\Twill\Http\Controllers\Admin\ModuleController as BaseModuleController;
use A17\Twill\Models\Contracts\TwillModelContract;
use A17\Twill\Services\Forms\Fields\BlockEditor;
use A17\Twill\Services\Forms\Fields\Input;
use A17\Twill\Services\Forms\Fields\Repeater;
use A17\Twill\Services\Forms\Form;
use A17\Twill\Services\Forms\InlineRepeater;
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
            // [tl! focus:start]
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
            // [tl! focus:end]
            BlockEditor::make()
        ]);
    }
}
```
