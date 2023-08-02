# Repeater

![screenshot](/assets/repeater.png)

:::tabs=currenttab.FormBuilder&items.FormBuilder|FormView|Directive:::
:::tab=name.FormBuilder:::

```php
Repeater::make()
    ->type('video')
```

:::#tab:::
:::tab=name.FormView:::

```blade
<x-twill::repeater
    type="video"
/>
```

:::#tab:::
:::tab=name.Directive:::

```blade
@formField('repeater', ['type' => 'video'])
```

:::#tab:::
:::#tabs:::

| Option       | Description                                  | Type    | Default value    |
|:-------------|:---------------------------------------------|:--------|:-----------------|
| type         | Type of repeater items                       | string  |                  |
| name         | Name of the field                            | string  | same as `type`   |
| max          | Maximum amount that can be created           | number  | null (unlimited) |
| buttonAsLink | Displays the `Add` button as a centered link | boolean | false            |
| reorder      | Allow reordering of repeater items           | boolean | true             |

<br/>

Repeater fields can be used inside as well as outside the block editor.

Inside the block editor, repeater blocks share the same model as regular blocks. By reading the section on
the [block editor](../5_block-editor) first, you will get a good overview of how to create and define repeater blocks
for
your project. No migration is needed when using repeater blocks. Refer to the section
titled [Adding repeater fields to a block](../5_block-editor/03_adding-repeater-fields-to-a-block.md) for a detailed
explanation.

Outside the block editor, repeater fields are used to save `hasMany` or `morphMany` relationships.

## Inline repeater

Inline repeaters are [Form builder](../3_modules/7_form-builder.md) only.

This field will allow you to create repeaters inline. This works for json repeaters or regular repeaters
and can both be used in block components and page forms.

While in theory these can be nested, nested inline repeaters only work on blocks not controller forms.

### JSON

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

## Blade repeater fields

The following example demonstrates how to define a relationship between `Team` and `TeamMember` modules to implement
a `team-member` repeater.

- Create the modules. Make sure to enable the `position` feature on the `TeamMember` module:

```
php artisan twill:make:module Team
php artisan twill:make:module TeamMember -P
```

- Update the `create_team_members_tables` migration. Add the `team_id` foreign key used for the `TeamMember—Team`
  relationship:

```php
class CreateTeamMembersTables extends Migration
{
    public function up()
    {
        Schema::create('team_members', function (Blueprint $table) {
            /* ... */

            $table->foreignId('team_id')
                ->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }
}
```

- Run the migrations:

```
php artisan migrate
```

- Update the `Team` model. Define the `members` relationship. The results should be ordered by position:

```php
class Team extends Model
{
    /* ... */

    public function members()
    {
        return $this->hasMany(TeamMember::class)->orderBy('position');
    }
}
```

- Update the `TeamMember` model. Add `team_id` to the `fillable` array:

```php
class TeamMember extends Model
{
    protected $fillable = [
        /* ... */
        'team_id',
    ];
}
```

- Update `TeamRepository`. Override the `afterSave` and `getFormFields` methods to process the repeater field:
    - Note: For Polymorphic relationships, use `updateRepeaterMorphMany` in place of `updateRepeater`

```php
class TeamRepository extends ModuleRepository
{
    /* ... */

    public function afterSave($object, $fields)
    {
        $this->updateRepeater($object, $fields, 'members', 'TeamMember', 'team-member');
        parent::afterSave($object, $fields);
    }

    public function getFormFields($object)
    {
        $fields = parent::getFormFields($object);
        $fields = $this->getFormFieldsForRepeater($object, $fields, 'members', 'TeamMember', 'team-member');
        return $fields;
    }
}
```

- Add the repeater Blade template:

Create file `resources/views/twill/repeaters/team-member.blade.php`:

```php
@twillRepeaterTitle('Team Member')
@twillRepeaterTrigger('Add member')
@twillRepeaterGroup('app')

<x-twill::input
    name="title"
    label="Title"
    :required="true"
/>

...
```

- Add the repeater field to the form:

Update file `resources/views/twill/teams/form.blade.php`:

```php
@extends('twill::layouts.form')

@section('contentFields')
    ...

    <x-twill::repeater
        type="team-member"
    />
@stop
```

- Finishing up:

Add both modules to your `twill.php` routes. Add the `Team` module to your `twill-navigation.php` config and you are
done!

## Dynamic repeater titles

In Twill >= 2.5, you can use the `@twillRepeaterTitleField` directive to include the value of a given field in the title
of the repeater items. This directive also accepts a `hidePrefix` option to hide the generic repeater title:

```php
@twillRepeaterTitle('Person')
@twillRepeaterTitleField('name', ['hidePrefix' => true])
@twillRepeaterTrigger('Add person')
@twillRepeaterGroup('app')

<x-twill::input
    name="name"
    label="Name"
    :required="true"
/>
```


