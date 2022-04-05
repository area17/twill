---
pageClass: twill-doc
---

# Repeater

![screenshot](/docs/_media/repeater.png)

```php
@formField('repeater', ['type' => 'video'])
```

| Option       | Description                                  | Type    | Default value    |
|:-------------|:---------------------------------------------|:--------|:-----------------|
| type         | Type of repeater items                       | string  |                  |
| name         | Name of the field                            | string  | same as `type`   |
| max          | Maximum amount that can be created           | number  | null (unlimited) |
| buttonAsLink | Displays the `Add` button as a centered link | boolean | false            |

<br/>

Repeater fields can be used inside as well as outside the block editor.

Inside the block editor, repeater blocks share the same model as regular blocks. By reading the section on the [block editor](/block-editor/) first, you will get a good overview of how to create and define repeater blocks for your project. No migration is needed when using repeater blocks. Refer to the section titled [Adding repeater fields to a block](/block-editor/adding-repeater-fields-to-a-block.html) for a detailed explanation.

Outside the block editor, repeater fields are used to save `hasMany` or `morphMany` relationships.

## Using repeater fields

The following example demonstrates how to define a relationship between `Team` and `TeamMember` modules to implement a `team-member` repeater.

- Create the modules. Make sure to enable the `position` feature on the `TeamMember` module:

```
php artisan twill:make:module Team
php artisan twill:make:module TeamMember -P
```

- Update the `create_team_members_tables` migration. Add the `team_id` foreign key used for the `TeamMember—Team` relationship:

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

@formField('input', [
    'name' => 'title',
    'label' => 'Title',
    'required' => true,
])

...
```

- Add the repeater field to the form:

Update file `resources/views/twill/teams/form.blade.php`:

```php
@extends('twill::layouts.form')

@section('contentFields')
    ...

    @formField('repeater', ['type' => 'team-member'])
@stop
```

- Finishing up:

Add both modules to your `twill.php` routes. Add the `Team` module to your `twill-navigation.php` config and you are done!

## Dynamic repeater titles

In Twill >= 2.5, you can use the `@twillRepeaterTitleField` directive to include the value of a given field in the title of the repeater items. This directive also accepts a `hidePrefix` option to hide the generic repeater title:

```php
@twillRepeaterTitle('Person')
@twillRepeaterTitleField('name', ['hidePrefix' => true])
@twillRepeaterTrigger('Add person')
@twillRepeaterGroup('app')

@formField('input', [
    'name' => 'name',
    'label' => 'Name',
    'required' => true,
])
```
