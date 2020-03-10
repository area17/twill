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

| Option      | Description                                                                                                              | Type/values                              | Default value |
| :---------- | :------------------------------------------------------------------------------------------------------------------------| :----------------------------------------| :------------ |
| name        | Name of the field                                                                                                        | string                                   |               |
| label       | Label of the field                                                                                                       | string                                   |               |
| type        | Type of input field                                                                                                      | text<br/>texarea<br/>number<br/>password | text          |
| translated  | Defines if the field is translatable                                                                                     | true<br/>false                           | false         |
| maxlength   | Max character count of the field                                                                                         | integer                                  |               |
| note        | Hint message displayed above the field                                                                                   | string                                   |               |
| placeholder | Text displayed as a placeholder in the field                                                                             | string                                   |               |
| rows        | Sets the number of rows in a textarea                                                                                    | integer                                  | 5             |
| required    | Displays an indicator that this field is required<br/>A backend validation rule is required to prevent users from saving | true<br/>false                           | false         |
| disabled    | Disables the field                                                                                                       | true<br />false                          | false         |
| readonly    | Sets the field as readonly                                                                                               | true<br />false                          | false         |
| default     | Sets a default value if empty                                                                                            | string                                   |               |


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
| type           | Type of input field                                          | text<br/>texarea<br/>number<br/>email                      | text                                    |
| toolbarOptions | Array of options/tools that will be displayed in the editor  | [Quill options](https://quilljs.com/docs/modules/toolbar/) | bold<br/>italic<br />underline<br/>link |
| editSource     | Displays a button to view source code                        | true<br />false                                            | false                                   |
| translated     | Defines if the field is translatable                         | true<br/>false                                             | false                                   |
| maxlength      | Max character count of the field                             | integer                                                    | 255                                     |
| note           | Hint message displayed above the field                       | string                                                     |                                         |
| placeholder    | Text displayed as a placeholder in the field                 | string                                                     |                                         |
| rows           | Sets the number of rows in a textarea                        | integer                                                    |                                         |
| required       | Displays an indicator that this field is required<br/>A backend validation rule is required to prevent users from saving | true<br/>false                                             | false                                   |


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
    'note' => 'Also used in listings'
    'fieldNote' => 'Minimum image width: 1500px'
])

@formField('medias', [
    'name' => 'slideshow',
    'label' => 'Slideshow',
    'max' => 5,
    'fieldNote' => 'Minimum image width: 1500px'
])
```

| Option         | Description                            | Type/values    | Default value |
| :------------- | :------------------------------------- | :------------- | :------------ |
| name           | Name of the field                      | string         |               |
| label          | Label of the field                     | string         |               |
| translated     | Defines if the field is translatable   | true<br/>false | false         |
| max            | Max number of attached items           | integer        | 1             |
| fieldNote      | Hint message displayed above the field | string         |               |
| note           | Hint message displayed in the field    | string         |               |


Right after declaring the `medias` formField in the blade template file, you still need to do a few things to make it works properly.

If the formField is in a static content form, you have to include the `HasMedias` Trait in your module's [Model](https://twill.io/docs/#models) and inlcude `HandleMedias` in your module's [Repository](https://twill.io/docs/#repositories), in addition, you have to uncomment the `$mediasParams` section in your Model file to let the model know about fields you'd like to save from the form.

Learn more about how Twill's media configurations work at [Model](https://twill.io/docs/#models), [Repository](https://twill.io/docs/#repositories), [Media Library Role & Crop Params](https://twill.io/docs/#image-rendering-service)

If the formField is used inside a block, you need to define the `mediasParams` at `config/twill.php` under `crops` key, and you are good to go. You could checkout [Twill Default Configuration](https://twill.io/docs/#default-configuration) and [Rendering Blocks](https://twill.io/docs/#rendering-blocks) for references.

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
    'name' => 'single_file_no_translate',
    'label' => 'Single file (no translate)',
    'note' => 'Add one file',
    'noTranslate' => true,
])

@formField('files', [
    'name' => 'files',
    'label' => 'Files',
    'noTranslate' => true,
    'max' => 4,
])
```

Similar to the media formField, to make the file field works, you have to include the `HasFiles` trait in your module's [Model](https://twill.io/docs/#models), and include `HandleFiles` trait in your module's [Repository](https://twill.io/docs/#repositories). At last, add the `filesParams` configuration array in your model.
```php
public $filesParams = ['file_role', ...]; // a list of file roles
```

Learn more at [Model](https://twill.io/docs/#models), [Repository](https://twill.io/docs/#repositories).

If you are using the file formField in a block, you have to define the `files` key in `config/twill.php`, put it under `block_editor` key and at the same level as `crops` key:
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
    $table->date_time('event_date')->nullable();
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

| Option      | Description                                                  | Type/values     | Default value |
| :---------- | :----------------------------------------------------------- | :-------------- | :------------ |
| name        | Name of the field                                            | string          |               |
| label       | Label of the field                                           | string          |               |
| options     | Array of options for the dropdown, must include _value_ and _label_ | array          |               |
| unpack      | Defines if the select will be displayed as an open list of options | true<br/>false  | false         |
| note        | Hint message displayed above the field                       | string          |               |
| placeholder | Text displayed as a placeholder in the field                 | string          |               |
| required    | Displays an indicator that this field is required<br/>A backend validation rule is required to prevent users from saving | true<br/>false  | false         |


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
![screenshot](/docs/_media/multiselect.png)

```php
@formField('multi_select', [
    'name' => 'sectors',
    'label' => 'Sectors',
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

@formField('multi_select', [
    'name' => 'sectors_bis',
    'label' => 'Sectors bis',
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
        ],
        [
            'value' => 'entertainment',
            'label' => 'Entertainment'
        ],
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
| note        | Hint message displayed above the field                       | string          |               |
| placeholder | Text displayed as a placeholder in the field                 | string          |               |
| required    | Displays an indicator that this field is required<br/>A backend validation rule is required to prevent users from saving | true<br/>false  | false         |
| disabled    | Disables the field                                           | true<br />false | false         |


There are several ways to implement a `multi_select` form field.

##### Multiselect with static values
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

##### Multiselect with dynamic values

Sometimes the content for the `multi_select` is coming from another model.

In this case that it can be implemented as follows:

- Create a Sectors [module](https://twill.io/docs/#cli-generator)

```
php artisan twill:module sectors
```

- Create a migration for a pivote table.

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

### Checkboxes

```php
@formField('checkbox', [
    'name' => 'featured',
    'label' => 'Featured'
])

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

### Radios

```php
@formField('radios', [
    'name' => 'discipline',
    'label' => 'Discipline',
    'default' => 'civic',
    'inline' => true/false,
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
| withoutSeparator | Defines if a separator before the block editor container should be rendered | true<br/>false | false         |


### Browser
![screenshot](/docs/_media/browser.png)

```php
@formField('browser', [
    'label' => 'Publications',
    'max' => 4,
    'name' => 'publications',
    'moduleName' => 'publications'
])
```

Browser fields can be used to save a `belongsToMany` relationship outside of the block editor.
Checkout this [Spectrum tutorial](https://spectrum.chat/twill/tips-and-tricks/step-by-step-ii-creating-a-twill-app~37c36601-1198-4c53-857a-a2b47c6d11aa) until we update this section to get more info on setting things up.
When using inside of the block editor, no migration is needed.

### Repeater
![screenshot](/docs/_media/repeater.png)

```php
@formField('repeater', ['type' => 'video'])
```

Repeaters fields can be used to save a `hasMany` relationship or a `morphMany` relationship outside of the block editor.
Checkout this [Github issue](https://github.com/area17/twill/issues/131) until we update this section to get more info on setting things up.
When using inside of the block editor, no migration is needed.

### Map
![screenshot](/docs/_media/map.png)

```php
@formField('map', [
    'name' => 'location',
    'label' => 'Location',
    'showMap' => true,
])
```

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


### Color

```php
@formField('color', [
    'name' => 'main_color',
    'label' => 'Main color'
])
```

A migration to save a `color` field would be:

```php
Schema::table('posts', function (Blueprint $table) {
    ...
    $table->string('main_color', 10)->nullable();
    ...
});
```
