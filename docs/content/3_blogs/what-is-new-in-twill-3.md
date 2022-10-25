# Twill 3

Twill 3 has been in development for quite some time. The work started somewhere beginning of 2022.

The future idea's for Twill were clear. As Twill is a developer focussed CMS framework, we wanted to create an even
better developer experience and improve our documentation.

As Twill 3's release is nearing, let's do an overview of the main new features that we are adding:

## Blade components for forms

In modern Laravel developments, it is almost certain that you have used [Blade](https://laravel.com/docs/9.x/blade),
Laravel's core templating engine.

Blade components are a great way to abstract html snippets and some logic into separate files. When used correctly it
will improve the readability of your template files a lot.

In Twill 2, forms would be defined by using the custom `@@formField()` directives.

And while this works great, it has one has some shortcomings. There is no ability to explore, formatting is not always
that easy and readability can sometimes be a bit difficult to maintain, on top of that, type errors are much harder to
detect.

With tools like [Laravel idea](https://laravel-idea.com) and [Blade lsp](https://github.com/haringsrob/laravel-dev-tools)
it becomes much easier to work with Blade components and it really offers the ability to explore.

<x-image alt="Example component autocomplete" path="./assets/demo-autocomplete-phpstorm.gif"/>

While we will remain supporting the old syntax until Twill 4, we suggest to make use of the new syntax instead:
<x-tabs-compare>
    <x-slot:new>
        <x-code lang="blade">
            @php
                echo <<<HTML
                @extends('twill::layouts.form')

                @section('contentFields')
                    <x-twill::wysiwyg
                        name="case_study"
                        label="Case study text"
                        :toolbar-options="['list-ordered', 'list-unordered']"
                        placeholder="Case study text"
                        :maxlength="200"
                        note="Hint message"
                    />

                    <x-twill::multi-select
                        name="sectors"
                        label="Sectors"
                        :min="1"
                        :max="2"
                        :options="[
                            [
                                'value' => 'arts',
                                'label' => 'Arts & Culture'
                            ],
                            [
                                'value' => 'finance',
                                'label' => 'Banking & Finance'
                            ],
                            [
                                'value' => 'civic',
                                'label' => 'Civic & Public'
                            ],
                            [
                                'value' => 'design',
                                'label' => 'Design & Architecture'
                            ],
                            [
                                'value' => 'education',
                                'label' => 'Education'
                            ]
                        ]"
                    />

                    <x-twill::block-editor/>
                @stop
                HTML
            @endphp
        </x-code>
    </x-slot:new>
    <x-slot:old>
        <x-code lang="blade" :content="<<<BLADE
@extends('twill::layouts.form')

@section('contentFields')
    @formField('wysiwyg', [
        'name' => 'case_study',
        'label' => 'Case study text',
        'toolbarOptions' => ['list-ordered', 'list-unordered'],
        'placeholder' => 'Case study text',
        'maxlength' => 200,
        'note' => 'Hint message',
    ])

    @formField('multi_select', [
        'name' => 'sectors',
        'label' => 'Sectors',
        'min' => 1,
        'max' => 2,
        'options' => [
            [
                'value' => 'arts',
                'label' => 'Arts & Culture'
            ],
            [
                'value' => 'finance',
                'label' => 'Banking & Finance'
            ],
            [
                'value' => 'civic',
                'label' => 'Civic & Public'
            ],
            [
                'value' => 'design',
                'label' => 'Design & Architecture'
            ],
            [
                'value' => 'education',
                'label' => 'Education'
            ]
        ]
    ])

    @formField('block_editor')
@stop
BLADE">
        </x-code>
    </x-slot:old>
</x-tabs-compare>

As time goes on, we will introduce more components for structuring forms, but all your fields are already there!

## Form builder

If you prefer to build your forms in php, we also have created a whole set of classes for you to do so.

<x-code>
<pre>
namespace App\Http\Controllers\Twill;

use A17\Twill\Models\Contracts\TwillModelContract;
use A17\Twill\Services\Forms\Fields\BlockEditor;
use A17\Twill\Services\Forms\Fields\Input;
use A17\Twill\Services\Forms\Form;
use A17\Twill\Http\Controllers\Admin\SingletonModuleController as BaseModuleController;

class HomepageController extends BaseModuleController
{
    protected $moduleName = 'homepages';

    public function getForm(TwillModelContract $model): Form // [tl! focus:start]
    {
        $form = parent::getForm($model);

        $form->add(
            Input::make()->name('description')->label('Description')->translatable()
        );

        $form->add(
            BlockEditor::make()
        );

        return $form;
    } // [tl! focus:end]
}
</pre>
</x-code>

## Table builder

Having clear tables in the cms backend can help content managers find content much quicker.

A default table in Twill shows the publishing status, title, languages and actions. But more often than not, there are
requirements to modify this.

While this was already possible by updating the array in `$indexColumns` in your module controller, we knew we could do
better.

With Twill 3, we have build a complete table builder that you can use to structure the table the way you want!

For example, you may want to display the description of your module alongside the default columns:

<x-code>
<pre>
namespace App\Http\Controllers\Twill;

use A17\Twill\Services\Listings\Columns\Text;
use A17\Twill\Services\Listings\TableColumns;
use A17\Twill\Http\Controllers\Admin\SingletonModuleController as BaseModuleController;

class HomepageController extends BaseModuleController
{
    protected $moduleName = 'homepages';

    protected function additionalIndexTableColumns(): TableColumns // [tl! focus:start]
    {
        $table = parent::additionalIndexTableColumns();

        $table->add(
            Text::make()->field('description')->title('Description')
        );

        return $table;
    } // [tl! focus:end]
}
</pre>
</x-code>

There are of course, many more fields that you can use such as: Image, Boolean, Featured and more.

You can check all these out in our documentation.
