## Form fields

Your module `form` view should look something like this (`resources/views/admin/moduleName/form.blade.php`):

```php
@extends('twill::layouts.form')
@section('contentFields')
    @formField('...', [...])
    ...
@stop
```

The idea of the `contentFields` section is to contain your most important fields and, if applicable, the block editor as the last field.

If you have other fields, like attributes, relationships, extra images, file attachments or repeaters, you'll want to add a `fieldsets` section after the `contentFields` section and use the `@formFieldset` directive to create new ones like in the following example:

```php
@extends('twill::layouts.form', [
    'additionalFieldsets' => [
        ['fieldset' => 'attributes', 'label' => 'Attributes'],
    ]
])

@section('contentFields')
    @formField('...', [...])
    ...
@stop

@section('fieldsets')
    @formFieldset(['id' => 'attributes', 'title' => 'Attributes'])
        @formField('...', [...])
        ...
    @endformFieldset
@stop
```

The additional fieldsets array passed to the form layout will display a sticky navigation of your fieldset on scroll.
You can also rename the content section by passing a `contentFieldsetLabel` property to the layout, or disable it entirely using
`'disableContentFieldset' => true`.

### Input
![screenshot](/docs/_media/input.png)

```php
@formField('input', [
    'name' => 'subtitle',
    'label' => 'Subtitle',
    'maxlength' => 100,
    'required' => true,
    'note' => 'Hint message goes here',
    'placeholder' => 'Placeholder goes here',
])

@formField('input', [
    'translated' => true,
    'name' => 'subtitle_translated',
    'label' => 'Subtitle (translated)',
    'maxlength' => 250,
    'required' => true,
    'note' => 'Hint message goes here',
    'placeholder' => 'Placeholder goes here',
    'type' => 'textarea',
    'rows' => 3
])
```

| Option      | Description                                                                                                              | Type/values                                        | Default value |
| :---------- | :------------------------------------------------------------------------------------------------------------------------| :------------------------------------------------- | :------------ |
| name        | Name of the field                                                                                                        | string                                             |               |
| label       | Label of the field                                                                                                       | string                                             |               |
| type        | Type of input field                                                                                                      | text<br/>texarea<br/>email<br/>number<br/>password | text          |
| translated  | Defines if the field is translatable                                                                                     | true<br/>false                                     | false         |
| maxlength   | Max character count of the field                                                                                         | integer                                            |               |
| note        | Hint message displayed above the field                                                                                   | string                                             |               |
| placeholder | Text displayed as a placeholder in the field                                                                             | string                                             |               |
| prefix      | Text displayed as a prefix in the field                                                                                  | string                                             |               |
| rows        | Sets the number of rows in a textarea                                                                                    | integer                                            | 5             |
| required    | Displays an indicator that this field is required<br/>A backend validation rule is required to prevent users from saving | true<br/>false                                     | false         |
| disabled    | Disables the field                                                                                                       | true<br />false                                    | false         |
| readonly    | Sets the field as readonly                                                                                               | true<br />false                                    | false         |
| default     | Sets a default value if empty                                                                                            | string                                             |               |


A migration to save an `input` field would be:

```php
Schema::table('articles', function (Blueprint $table) {
    ...
    $table->string('subtitle', 100)->nullable();
    ...

});
// OR
Schema::table('article_translations', function (Blueprint $table) {
    ...
    $table->string('subtitle', 250)->nullable();
    ...
});
```

If this `input` field is used for longer strings then the migration would be:

```php
Schema::table('articles', function (Blueprint $table) {
    ...
    $table->text('subtitle')->nullable();
    ...
});
```

When used in a [block](https://twill.io/docs/#adding-blocks), no migration is needed.


### WYSIWYG
![screenshot](/docs/_media/wysiwyg.png)

```php
@formField('wysiwyg', [
    'name' => 'case_study',
    'label' => 'Case study text',
    'toolbarOptions' => ['list-ordered', 'list-unordered'],
    'placeholder' => 'Case study text',
    'maxlength' => 200,
    'note' => 'Hint message',
])

@formField('wysiwyg', [
    'name' => 'case_study',
    'label' => 'Case study text',
    'toolbarOptions' => [ [ 'header' => [1, 2, false] ], 'list-ordered', 'list-unordered', [ 'indent' => '-1'], [ 'indent' => '+1' ] ],
    'placeholder' => 'Case study text',
    'maxlength' => 200,
    'editSource' => true,
    'note' => 'Hint message',
])
```
By default, the WYSIWYG field is based on [Quill](https://quilljs.com/).

You can add all [toolbar options](https://quilljs.com/docs/modules/toolbar/) from Quill with the `toolbarOptions` key.

For example, this configuration will render a `wysiwyg` field with almost all features from Quill and Snow theme.

```php
 @formField('wysiwyg', [
    'name' => 'case_study',
    'label' => 'Case study text',
    'toolbarOptions' => [
      ['header' => [2, 3, 4, 5, 6, false]],
      'bold',
      'italic',
      'underline',
      'strike',
      ["script" => "super"],
      ["script" => "sub"],
      "blockquote",
      "code-block",
      ['list' => 'ordered'],
      ['list' => 'bullet'],
      ['indent' => '-1'],
      ['indent' => '+1'],
      ["align" => []],
      ["direction" => "rtl"],
      'link',
      "clean",
    ],
    'placeholder' => 'Case study text',
    'maxlength' => 200,
    'editSource' => true,
    'note' => 'Hint message`',
 ])
```

Note that Quill outputs CSS classes in the HTML for certain toolbar modules (indent, font, align, etc.), and that the image module is not integrated with Twill's media library. It outputs the base64 representation of the uploaded image. It is not a recommended way of using and storing images, prefer using one or multiple `medias` form fields or blocks fields for flexible content. This will give you greater control over your frontend output.


| Option         | Description                                                  | Type/values                                                | Default value                           |
| :------------- | :----------------------------------------------------------- | :--------------------------------------------------------- | :-------------------------------------- |
| name           | Name of the field                                            | string                                                     |                                         |
| label          | Label of the field                                           | string                                                     |                                         |
| type           | Type of wysiwyg field                                        | quill<br/>tiptap                                           | quill                                   |
| toolbarOptions | Array of options/tools that will be displayed in the editor  | [Quill options](https://quilljs.com/docs/modules/toolbar/) | bold<br/>italic<br />underline<br/>link |
| editSource     | Displays a button to view source code                        | true<br />false                                            | false                                   |
| hideCounter    | Hide the character counter displayed at the bottom           | true<br />false                                            | false                                   |
| limitHeight    | Limit the editor height from growing beyond the viewport     | true<br />false                                            | false                                   |
| translated     | Defines if the field is translatable                         | true<br/>false                                             | false                                   |
| maxlength      | Max character count of the field                             | integer                                                    | 255                                     |
| note           | Hint message displayed above the field                       | string                                                     |                                         |
| placeholder    | Text displayed as a placeholder in the field                 | string                                                     |                                         |
| required       | Displays an indicator that this field is required<br/>A backend validation rule is required to prevent users from saving | true<br/>false | false |


A migration to save a `wysiwyg` field would be:

```php
Schema::table('articles', function (Blueprint $table) {
    ...
    $table->text('case_study')->nullable();
    ...

});
// OR
Schema::table('article_translations', function (Blueprint $table) {
    ...
    $table->text('case_study')->nullable();
    ...
});
```
When used in a [block](https://twill.io/docs/#adding-blocks), no migration is needed.

### Medias
![screenshot](/docs/_media/medias.png)

```php
@formField('medias', [
    'name' => 'cover',
    'label' => 'Cover image',
    'note' => 'Also used in listings',
    'fieldNote' => 'Minimum image width: 1500px'
])

@formField('medias', [
    'name' => 'slideshow',
    'label' => 'Slideshow',
    'max' => 5,
    'fieldNote' => 'Minimum image width: 1500px'
])
```

| Option         | Description                                          | Type/values    | Default value |
| :------------- | :--------------------------------------------------- | :------------- | :------------ |
| name           | Name of the field                                    | string         |               |
| label          | Label of the field                                   | string         |               |
| translated     | Defines if the field is translatable                 | true<br/>false | false         |
| max            | Max number of attached items                         | integer        | 1             |
| fieldNote      | Hint message displayed above the field               | string         |               |
| note           | Hint message displayed in the field                  | string         |               |
| buttonOnTop    | Displays the `Attach images` button above the images | true<br/>false | false         |


Right after declaring the `medias` formField in the blade template file, you still need to do a few things to make it work properly.

If the formField is in a static content form, you have to include the `HasMedias` Trait in your module's [Model](https://twill.io/docs/#models) and inlcude `HandleMedias` in your module's [Repository](https://twill.io/docs/#repositories). In addition, you have to uncomment the `$mediasParams` section in your Model file to let the model know about fields you'd like to save from the form.

Learn more about how Twill's media configurations work at [Model](https://twill.io/docs/#models), [Repository](https://twill.io/docs/#repositories), [Media Library Role & Crop Params](https://twill.io/docs/#image-rendering-service)

If the formField is used inside a block, you need to define the `mediasParams` at `config/twill.php` under `crops` key, and you are good to go. You could checkout [Twill Default Configuration](https://twill.io/docs/#default-configuration) and [Rendering Blocks](https://twill.io/docs/#rendering-blocks) for references.

If the formField is used inside a repeater, you need to define the `mediasParams` at `config/twill.php` under `block_editor.crops`.

If you need medias fields to be translatable (ie. publishers can select different images for each locale), set the `twill.media_library.translated_form_fields` configuration value to `true`.

##### Example:
To add a `medias` form field in a form, first add `$mediaParams` to the model.

```php
<?php

namespace App\Models;

...
use A17\Twill\Models\Behaviors\HasMedias;
...
use A17\Twill\Models\Model;

class Post extends Model
{
    use ..., HasMedias;

    ...
    public $mediasParams = [
        'cover' => [
            'default' => [
                [
                    'name' => 'default',
                    'ratio' => 16 / 9,
                ],
            ],
            'mobile' => [
                [
                    'name' => 'mobile',
                    'ratio' => 1,
                ],
            ],
        ],
    ];

    ...
}
```

Then, add the form field to the `form.blade.php` file.

```php
@extends('twill::layouts.form')

@section('contentFields')

    ...

    @formField('medias', [
        'name' => 'cover',
        'label' => 'Cover image',
    ])

    ...
@stop
```

No migration is needed to save `medias` form fields.


### Files
![screenshot](/docs/_media/files.png)

```php
@formField('files', [
    'name' => 'single_file',
    'label' => 'Single file',
    'note' => 'Add one file (per language)'
])

@formField('files', [
    'name' => 'files',
    'label' => 'Files',
    'max' => 4,
])
```

| Option         | Description                               | Type/values    | Default value |
| :------------- | :---------------------------------------- | :------------- | :------------ |
| name           | Name of the field                         | string         |               |
| label          | Label of the field                        | string         |               |
| itemLabel      | Label used for the `Add` button           | string         |               |
| max            | Max number of attached items              | integer        | 1             |
| fieldNote      | Hint message displayed above the field    | string         |               |
| note           | Hint message displayed in the field       | string         |               |
| buttonOnTop    | Displays the `Add` button above the files | true<br/>false | false         |


Similar to the media formField, to make the file field work, you have to include the `HasFiles` trait in your module's [Model](https://twill.io/docs/#models), and include `HandleFiles` trait in your module's [Repository](https://twill.io/docs/#repositories). At last, add the `filesParams` configuration array in your model.
```php
public $filesParams = ['file_role', ...]; // a list of file roles
```

Learn more at [Model](https://twill.io/docs/#models), [Repository](https://twill.io/docs/#repositories).

If you are using the file formField in a block, you have to define the `files` key in `config/twill.php`. Add it under `block_editor` key and at the same level as `crops` key:
```php
return [
    'block_editor' => [
        'crops' => [
            ...
        ],
        'files' => ['file_role1', 'file_role2', ...]
    ]
```

No migration is needed to save `files` form fields.


### Datepicker
![screenshot](/docs/_media/datepicker.png)

```php
@formField('date_picker', [
    'name' => 'event_date',
    'label' => 'Event date',
    'minDate' => '2017-09-10 12:00',
    'maxDate' => '2017-12-10 12:00'
])
```

| Option      | Description                                                  | Type/values     | Default value |
| :---------- | :----------------------------------------------------------- | :-------------- | :------------ |
| name        | Name of the field                                            | string          |               |
| label       | Label of the field                                           | string          |               |
| minDate     | Minimum selectable date                                      | string          |               |
| maxDate     | Maximum selectable date                                      | string          |               |
| withTime    | Define if the field will display the time selector           | true<br/>false  | true          |
| time24Hr    | Pick time with a 24h picker instead of AM/PM                 | true<br/>false  | false         |
| allowClear  | Adds a button to clear the field                             | true<br/>false  | false         |
| allowInput  | Allow manually editing the selected date in the field        | true<br/>false  | false         |
| altFormat   | Format used by [flatpickr](https://flatpickr.js.org/formatting/) | string          | F j, Y        |
| hourIncrement  | Time picker hours increment        | number  | 1         |
| minuteIncrement  | Time picker minutes increment        | number  | 30         |
| note        | Hint message displayed above the field                       | string          |               |
| required    | Displays an indicator that this field is required<br/>A backend validation rule is required to prevent users from saving | true<br/>false  | false         |


A migration to save a `date_picker` field would be:

```php
Schema::table('posts', function (Blueprint $table) {
    ...
    $table->date('event_date')->nullable();
    ...
});
// OR
Schema::table('posts', function (Blueprint $table) {
    ...
    $table->dateTime('event_date')->nullable();
    ...
});
```

When used in a [block](https://twill.io/docs/#adding-blocks), no migration is needed.

### Timepicker

```php
@formField('time_picker', [
    'name' => 'event_time',
    'label' => 'Event time',
])
```

| Option      | Description                                                  | Type/values     | Default value |
| :---------- | :----------------------------------------------------------- | :-------------- | :------------ |
| name        | Name of the field                                            | string          |               |
| label       | Label of the field                                           | string          |               |
| time24Hr    | Pick time with a 24h picker instead of AM/PM                 | true<br/>false  | false         |
| allowClear  | Adds a button to clear the field                             | true<br/>false  | false         |
| allowInput  | Allow manually editing the selected date in the field        | true<br/>false  | false         |
| hourIncrement  | Time picker hours increment        | number  | 1         |
| minuteIncrement  | Time picker minutes increment        | number  | 30         |
| altFormat   | Format used by [flatpickr](https://flatpickr.js.org/formatting/) | string          | h:i        |
| note        | Hint message displayed above the field                       | string          |               |
| required    | Displays an indicator that this field is required<br/>A backend validation rule is required to prevent users from saving | true<br/>false  | false         |


A migration to save a `time_picker` field would be:

```php
Schema::table('posts', function (Blueprint $table) {
    ...
    $table->time('event_time')->nullable();
    ...
});
// OR, if you are merging with a date field
Schema::table('posts', function (Blueprint $table) {
    ...
    $table->dateTime('event_date')->nullable();
    ...
});
```

When used in a [block](https://twill.io/docs/#adding-blocks), no migration is needed.


### Select
![screenshot](/docs/_media/select.png)

```php
@formField('select', [
    'name' => 'office',
    'label' => 'Office',
    'placeholder' => 'Select an office',
    'options' => [
        [
            'value' => 1,
            'label' => 'New York'
        ],
        [
            'value' => 2,
            'label' => 'London'
        ],
        [
            'value' => 3,
            'label' => 'Berlin'
        ]
    ]
])
```

| Option      | Description                                                  | Type/values     | Default value |
| :---------- | :----------------------------------------------------------- | :-------------- | :------------ |
| name        | Name of the field                                            | string          |               |
| label       | Label of the field                                           | string          |               |
| options     | Array of options for the dropdown, must include _value_ and _label_ | array          |               |
| unpack      | Defines if the select will be displayed as an open list of options | true<br/>false  | false         |
| columns     | Aligns the options on a grid with a given number of columns  | integer         | 0 (off)       |
| searchable  | Filter the field values while typing                         | true<br/>false  | false         |
| note        | Hint message displayed above the field                       | string          |               |
| placeholder | Text displayed as a placeholder in the field                 | string          |               |
| required    | Displays an indicator that this field is required<br/>A backend validation rule is required to prevent users from saving | true<br/>false  | false         |


A migration to save a `select` field would be:

```php
Schema::table('posts', function (Blueprint $table) {
    ...
    $table->integer('office')->nullable();
    ...
});
```

When used in a [block](https://twill.io/docs/#adding-blocks), no migration is needed.

### Select unpacked
![screenshot](/docs/_media/selectunpacked.png)

```php
@formField('select', [
    'name' => 'discipline',
    'label' => 'Discipline',
    'unpack' => true,
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
        ],
        [
            'value' => 'entertainment',
            'label' => 'Entertainment'
        ],
    ]
])
```

A migration to save the above `select` field would be:

```php
Schema::table('posts', function (Blueprint $table) {
    ...
    $table->string('discipline')->nullable();
    ...
});
```

When used in a [block](https://twill.io/docs/#adding-blocks), no migration is needed.


### Multi select
![screenshot](/docs/_media/multiselectunpacked.png)

```php
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
```

| Option      | Description                                                  | Type/values     | Default value |
| :---------- | :----------------------------------------------------------- | :-------------- | :------------ |
| name        | Name of the field                                            | string          |               |
| label       | Label of the field                                           | string          |               |
| min         | Minimum number of selectable options                         | integer         |               |
| max         | Maximum number of selectable options                         | integer         |               |
| options     | Array of options for the dropdown, must include _value_ and _label_ | array           |               |
| unpack      | Defines if the multi select will be displayed as an open list of options | true<br/>false  | true         |
| columns     | Aligns the options on a grid with a given number of columns  | integer         | 0 (off)       |
| searchable  | Filter the field values while typing                         | true<br/>false  | false         |
| note        | Hint message displayed above the field                       | string          |               |
| placeholder | Text displayed as a placeholder in the field                 | string          |               |
| required    | Displays an indicator that this field is required<br/>A backend validation rule is required to prevent users from saving | true<br/>false  | false         |
| disabled    | Disables the field                                           | true<br />false | false         |


There are several ways to implement a `multi_select` form field.

##### Multi select with static values
Sometimes you just have a set of values that are static.

In this case that it can be implemented as follows:

- Create the database migration to store a JSON or LONGTEXT:
```php
Schema::table('posts', function (Blueprint $table) {
    ...
    $table->json('sectors')->nullable();
    ...
});

// OR
Schema::table('posts', function (Blueprint $table) {
    ...
    $table->longtext('sectors')->nullable();
    ...
});
```

- In your model add an accessor and a mutator:
```php
public function getSectorsAttribute($value)
{
    return collect(json_decode($value))->map(function($item) {
        return ['id' => $item];
    })->all();
}

public function setSectorsAttribute($value)
{
    $this->attributes['sectors'] = collect($value)->filter()->values();
}
```

- Cast the field to `array`:
```php
protected $casts = [
    'sectors' => 'array'
]
```

##### Multi select with dynamic values

Sometimes the content for the `multi_select` is coming from another model.

In this case that it can be implemented as follows:

- Create a Sectors [module](https://twill.io/docs/#cli-generator)

```
php artisan twill:module sectors
```

- Create a migration for a pivot table.

```
php artisan make:migration create_post_sector_table
```

- Use Twill's `createDefaultRelationshipTableFields` to set it up:

```php
public function up()
{
    Schema::create('post_sector', function (Blueprint $table) {
        createDefaultRelationshipTableFields($table, 'sector', 'post');
        $table->integer('position')->unsigned()->index();
    });
}
```

- In your model, add a `belongsToMany` relationship:

```php
public function sectors() {
    return $this->belongsToMany('App\Models\Sector');
}
```

- In your repository, make sure to sync the association when saving:

```php
public function afterSave($object, $fields)
{
    $object->sectors()->sync($fields['sectors'] ?? []);

    parent::afterSave($object, $fields);
}
```

- In your controller, add to the formData the collection of options:
```php
protected function formData($request)
{
    return [
        'sectors' => app()->make(SectorRepository::class)->listAll()
    ];
}
```

- In the form, we can now add the field:
```php
@formField('multi_select', [
    'name' => 'sectors',
    'label' => 'Sectors',
    'options' => $sectors
])
```

When used in a [block](https://twill.io/docs/#adding-blocks), no migration is needed.


### Multi select inline
![screenshot](/docs/_media/multiselectinline.png)

```php
@formField('multi_select', [
    'name' => 'sectors',
    'label' => 'Sectors',
    'unpack' => false,
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
```

See [Multi select](https://twill.io/docs/#multi-select) for more information on how to implement the field with static and dynamic values.


### Checkbox
![screenshot](/docs/_media/checkbox.png)

```php
@formField('checkbox', [
    'name' => 'featured',
    'label' => 'Featured'
])
```

| Option              | Description                                             | Type            | Default value |
| :------------------ | :------------------------------------------------------ | :-------------- | :------------ |
| name                | Name of the field                                       | string          |               |
| label               | Label of the field                                      | string          |               |
| note                | Hint message displayed above the field                  | string          |               |
| default             | Sets a default value                                    | boolean         | false         |
| disabled            | Disables the field                                      | boolean         | false         | 
| requireConfirmation | Displays a confirmation dialog when modifying the field | boolean         | false         |
| confirmTitleText    | The title of the confirmation dialog                    | string          | 'Confirm selection' |
| confirmMessageText  | The text of the confirmation dialog                     | string          | 'Are you sure you want to change this option ?' |
| border              | Draws a border around the field                         | boolean         | false         |


### Multiple checkboxes
![screenshot](/docs/_media/checkboxes.png)

```php
@formField('checkboxes', [
    'name' => 'sectors',
    'label' => 'Sectors',
    'note' => '3 sectors max & at least 1 sector',
    'min' => 1,
    'max' => 3,
    'inline' => true,
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
    ]
])
```

| Option  | Description                                                         | Type    | Default value |
| :------ | :------------------------------------------------------------------ | :-------| :------------ |
| name    | Name of the field                                                   | string  |               |
| label   | Label of the field                                                  | string  |               |
| min     | Minimum number of selectable options                                | integer |               |
| max     | Maximum number of selectable options                                | integer |               |
| options | Array of options for the dropdown, must include _value_ and _label_ | array   |               |
| inline  | Defines if the options are displayed on one or multiple lines       | boolean | false         |
| note    | Hint message displayed above the field                              | string  |               |
| border  | Draws a border around the field                                     | boolean | false         |
| columns | Aligns the options on a grid with a given number of columns         | integer | 0 (off)       |


### Radios
![screenshot](/docs/_media/radios.png)

```php
@formField('radios', [
    'name' => 'discipline',
    'label' => 'Discipline',
    'default' => 'civic',
    'inline' => true,
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
    ]
])
```

| Option              | Description                                                         | Type    | Default value |
| :------------------ | :------------------------------------------------------------------ | :------ | :------------ |
| name                | Name of the field                                                   | string  |               |
| label               | Label of the field                                                  | string  |               |
| note                | Hint message displayed above the field                              | string  |               |
| options             | Array of options for the dropdown, must include _value_ and _label_ | array   |               |
| inline              | Defines if the options are displayed on one or multiple lines       | boolean | false         |
| default             | Sets a default value                                                | string  |               |
| requireConfirmation | Displays a confirmation dialog when modifying the field             | boolean | false         |
| confirmTitleText    | The title of the confirmation dialog                                | string  | 'Confirm selection' |
| confirmMessageText  | The text of the confirmation dialog                                 | string  | 'Are you sure you want to change this option ?' |
| required            | Displays an indicator that this field is required<br/>A backend validation rule is required to prevent users from saving | boolean | false |
| border              | Draws a border around the field                                     | boolean | false         |
| columns             | Aligns the options on a grid with a given number of columns         | integer | 0 (off)       |


### Block editor
![screenshot](/docs/_media/blockeditor.png)

```php
@formField('block_editor', [
    'blocks' => ['title', 'quote', 'text', 'image', 'grid', 'test', 'publications', 'news']
])
```

See [Block editor](https://twill.io/docs/#block-editor-3)


| Option           | Description                                                  | Type/values    | Default value |
| :--------------- | :----------------------------------------------------------- | :------------- | :------------ |
| blocks           | Array of blocks                                              | array          |               |
| label            | Label used for the button                                    | string         | 'Add Content' |
| withoutSeparator | Defines if a separator before the block editor container should be rendered | true<br/>false | false         |


### Browser
![screenshot](/docs/_media/browser.png)

```php
@formField('browser', [
    'moduleName' => 'publications',
    'name' => 'publications',
    'label' => 'Publications',
    'max' => 4,
])
```

| Option      | Description                                                                     | Type    | Default value |
| :---------- | :------------------------------------------------------------------------------ | :-------| :------------ |
| name        | Name of the field                                                               | string  |               |
| label       | Label of the field                                                              | string  |               |
| moduleName  | Name of the module (single related module)                                      | string  |               |
| modules     | Array of modules (multiple related modules), must include _name_                | array   |               |
| endpoints   | Array of endpoints (multiple related modules), must include _value_ and _label_ | array   |               |
| max         | Max number of attached items                                                    | integer | 1             |
| note        | Hint message displayed in the field                                             | string  |               |
| fieldNote   | Hint message displayed above the field                                          | string  |               |
| browserNote | Hint message displayed inside the browser modal                                 | string  |               |
| itemLabel   | Label used for the `Add` button                                                 | string  |               |
| buttonOnTop | Displays the `Add` button above the items                                       | boolean | false         |
| wide        | Expands the browser modal to fill the viewport                                  | boolean | false         |
| sortable    | Allows manually sorting the attached items                                      | boolean | true          |

<br/>

Browser fields can be used inside as well as outside the block editor.

Inside the block editor, no migration is needed when using browsers. Refer to the section titled [Adding browser fields to a block](#adding-browser-fields-to-a-block) for a detailed explanation.

Outside the block editor, browser fields are used to save `belongsToMany` relationships. The relationships can be stored in Twill's own `related` table or in a custom pivot table.

#### Using browser fields as related items

The following example demonstrates how to use a browser field to attach `Authors` to `Articles`. 

- Update the `Article` model to add the `HasRelated` trait:

```php
use A17\Twill\Models\Behaviors\HasRelated;

class Article extends Model
{
    use HasRelated;

    /* ... */
}
```

- Update `ArticleRepository` to add the browser field to the `$relatedBrowsers` property:

```php
class ArticleRepository extends ModuleRepository
{
    protected $relatedBrowsers = ['authors'];
}
```

- Add the browser field to `resources/views/admin/articles/form.blade.php`:

```php
@extends('twill::layouts.form')

@section('contentFields')
    ...

    @formField('browser', [
        'moduleName' => 'authors',
        'name' => 'authors',
        'label' => 'Authors',
        'max' => 4,
    ])
@stop
```

#### Multiple modules as related items

You can use the same approach to handle polymorphic relationships through Twill's `related` table.

- Update `ArticleRepository`:

```php
class ArticleRepository extends ModuleRepository
{
    protected $relatedBrowsers = ['collaborators'];
}
```

- Add the browser field to `resources/views/admin/articles/form.blade.php`:

```php
@extends('twill::layouts.form')

@section('contentFields')
    ...

    @formField('browser', [
        'modules' => [
            [
              'label' => 'Authors',
              'name' => 'authors',
            ],
            [
              'label' => 'Editors',
              'name' => 'editors',
            ],
        ],
        'name' => 'collaborators',
        'label' => 'Collaborators',
        'max' => 4,
    ])
@stop
```

- Alternatively, you can use manual endpoints instead of module names:

```php
    @formField('browser', [
        'endpoints' => [
            [
              'label' => 'Authors',
              'value' => '/authors/browser',
            ],
            [
              'label' => 'Editors',
              'value' => '/editors/browser',
            ],
        ],
        'name' => 'collaborators',
        'label' => 'Collaborators',
        'max' => 4,
    ])
```

#### Working with related items

To retrieve the items in the frontend, you can use the `getRelated` method on models and blocks. It will return of collection of related models in the correct order:

```php
    $item->getRelated('collaborators');

    // or, in a block:

    $block->getRelated('collaborators');
```

#### Using browser fields and custom pivot tables

Checkout this [Spectrum tutorial](https://spectrum.chat/twill/tips-and-tricks/step-by-step-ii-creating-a-twill-app~37c36601-1198-4c53-857a-a2b47c6d11aa) that walks through the entire process of using browser fields with custom pivot tables.

### Repeater
![screenshot](/docs/_media/repeater.png)

```php
@formField('repeater', ['type' => 'video'])
```

| Option       | Description                                   | Type    | Default value  |
| :----------- | :-------------------------------------------- | :-------| :------------- |
| type         | Type of repeater items                        | string  |                |
| name         | Name of the field                             | string  | same as `type` |
| buttonAsLink | Displays the `Add` button as a centered link  | boolean | false          |

<br/>

Repeater fields can be used inside as well as outside the block editor.

Inside the block editor, repeater blocks share the same model as regular blocks. By reading the section on the [block editor](#block-editor-3) first, you will get a good overview of how to create and define repeater blocks for your project. No migration is needed when using repeater blocks. Refer to the section titled [Adding repeater fields to a block](#adding-repeater-fields-to-a-block) for a detailed explanation.

Outside the block editor, repeater fields are used to save `hasMany` or `morphMany` relationships.

#### Using repeater fields

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

Create file `resources/views/admin/repeaters/team-member.blade.php`:

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

Update file `resources/views/admin/teams/form.blade.php`:

```php
@extends('twill::layouts.form')

@section('contentFields')
    ...

    @formField('repeater', ['type' => 'team-member'])
@stop
```

- Finishing up:

Add both modules to your `admin.php` routes. Add the `Team` module to your `twill-navigation.php` config and you are done!

#### Dynamic repeater titles

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


### Map
![screenshot](/docs/_media/map.png)

```php
@formField('map', [
    'name' => 'location',
    'label' => 'Location',
    'showMap' => true,
])
```

| Option           | Description                                                 | Type/values     | Default value |
| :--------------- | :---------------------------------------------------------- | :-------------- | :------------ |
| name             | Name of the field                                           | string          |               |
| label            | Label of the field                                          | string          |               |
| showMap          | Adds a button to toggle the map visibility                  | true<br />false | true          |
| openMap          | Used with `showMap`, initialize the field with the map open | true<br />false | false          |
| saveExtendedData | Enables saving Bounding Box Coordinates and Location types  | true<br />false | false         |

This field requires that you provide a `GOOGLE_MAPS_API_KEY` variable in your .env file.

A migration to save a `map` field would be:

```php
Schema::table('posts', function (Blueprint $table) {
    ...
    $table->json('location')->nullable();
    ...
});
```

The field used should also be casted as an array in your model:

```php
public $casts = [
    'location' => 'array',
];
```

When used in a [block](https://twill.io/docs/#adding-blocks), no migration is needed.

#### Example of data stored in the Database:
Default data:

```javascript
{
    "latlng": "48.85661400000001|2.3522219",
    "address": "Paris, France"
}
```

Extended data:

```javascript
{
    "latlng": "51.1808302|-2.256022799999999",
    "address": "Warminster BA12 7LG, United Kingdom",
    "types": ["point_of_interest", "establishment"],
    "boundingBox": {
        "east": -2.25289275,
        "west": -2.257066149999999,
        "north": 51.18158853029149,
        "south": 51.17889056970849
    }
}
```

### Color
![screenshot](/docs/_media/color.png)

```php
@formField('color', [
    'name' => 'main_color',
    'label' => 'Main color'
])
```

| Option  | Description         | Type     | Default value |
| :------ | :------------------ | :------- | :------------ |
| name    | Name of the field   | string   |               |
| label   | Label of the field  | string   |               |


A migration to save a `color` field would be:

```php
Schema::table('posts', function (Blueprint $table) {
    ...
    $table->string('main_color', 10)->nullable();
    ...
});
```
### Conditional fields

You can conditionally display fields based on the values of other fields in your form. For example, if you wanted to display a video embed text field only if the type of article, a radio field, is "video" you'd do something like the following:

```php
@formField('radios', [
    'name' => 'type',
    'label' => 'Article type',
    'default' => 'long_form',
    'inline' => true,
    'options' => [
        [
            'value' => 'long_form',
            'label' => 'Long form article'
        ],
        [
            'value' => 'video',
            'label' => 'Video article'
        ]
    ]
])

@formConnectedFields([
    'fieldName' => 'type',
    'fieldValues' => 'video',
    'renderForBlocks' => true/false # (depending on regular form vs block form)
])
    @formField('input', [
        'name' => 'video_embed',
        'label' => 'Video embed'
    ])
@endformConnectedFields
```
Here's an example based on a checkbox field where the value is either true or false:

```php
@formField('checkbox', [
    'name' => 'vertical_article',
    'label' => 'Vertical Story'
])

@formConnectedFields([
    'fieldName' => 'vertical_article',
    'fieldValues' => true,
    'renderForBlocks' => true/false # (depending on regular form vs block form)
])
    @formField('medias', [
        'name' => 'vertical_image',
        'label' => 'Vertical Image',
    ])
@endformConnectedFields
```
