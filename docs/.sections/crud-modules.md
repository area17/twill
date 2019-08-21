## CRUD modules

### CLI Generator
You can generate all the files needed in your application to create a new CRUD module using Twill's Artisan generator:

```bash
php artisan twill:module yourPluralModuleName
```

The command has a couple of options :
- `--hasBlocks (-B)`,
- `--hasTranslation (-T)`,
- `--hasSlug (-S)`,
- `--hasMedias (-M)`,
- `--hasFiles (-F)`,
- `--hasPosition (-P)`
- `--hasRevisions(-R)`.

This will generate a migration file, a model, a repository, a controller, a form request object and a form view.

Start by filling in the migration and models using the documentation below.

Add `Route::module('yourPluralModuleName');` to your admin routes file.

Setup a new CMS menu item in `config/twill-navigation.php`.

Depending on the depth of your module in your navigation, you'll need to wrap your route declaration in one or multiple nested route groups.

Setup your form fields in `resources/views/admin/moduleName/form.blade.php`.

Setup your index options and columns in your controller if needed.

Enjoy.

### Migrations
Generated migrations are regular Laravel migrations. A few helpers are available to create the default fields any CRUD module will use:

```php
<?php

// main table, holds all non translated fields
Schema::create('table_name_plural', function (Blueprint $table) {
    createDefaultTableFields($table)
    // will add the following inscructions to your migration file
    // $table->increments('id');
    // $table->softDeletes();
    // $table->timestamps();
    // $table->boolean('published');
});

// translation table, holds translated fields
Schema::create('table_name_singular_translations', function (Blueprint $table) {
    createDefaultTranslationsTableFields($table, 'tableNameSingular')
    // will add the following inscructions to your migration file
    // createDefaultTableFields($table);
    // $table->string('locale', 6)->index();
    // $table->boolean('active');
    // $table->integer("{$tableNameSingular}_id")->unsigned();
    // $table->foreign("{$tableNameSingular}_id", "fk_{$tableNameSingular}_translations_{$tableNameSingular}_id")->references('id')->on($table)->onDelete('CASCADE');
    // $table->unique(["{$tableNameSingular}_id", 'locale']);
});

// slugs table, holds slugs history
Schema::create('table_name_singular_slugs', function (Blueprint $table) {
    createDefaultSlugsTableFields($table, 'tableNameSingular')
    // will add the following inscructions to your migration file
    // createDefaultTableFields($table);
    // $table->string('slug');
    // $table->string('locale', 6)->index();
    // $table->boolean('active');
    // $table->integer("{$tableNameSingular}_id")->unsigned();
    // $table->foreign("{$tableNameSingular}_id", "fk_{$tableNameSingular}_translations_{$tableNameSingular}_id")->references('id')->on($table)->onDelete('CASCADE')->onUpdate('NO ACTION');
});

// revisions table, holds revision history
Schema::create('table_name_singular_revisions', function (Blueprint $table) {
    createDefaultRevisionTableFields($table, 'tableNameSingular');
    // will add the following inscructions to your migration file
    // $table->increments('id');
    // $table->timestamps();
    // $table->json('payload');
    // $table->integer("{$tableNameSingular}_id")->unsigned()->index();
    // $table->integer('user_id')->unsigned()->nullable();
    // $table->foreign("{$tableNameSingular}_id")->references('id')->on("{$tableNamePlural}")->onDelete('cascade');
    // $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
});

// related content table, holds many to many association between 2 tables
Schema::create('table_name_singular1_table_name_singular2', function (Blueprint $table) {
    createDefaultRelationshipTableFields($table, $table1NameSingular, $table2NameSingular)
    // will add the following inscructions to your migration file
    // $table->integer("{$table1NameSingular}_id")->unsigned();
    // $table->foreign("{$table1NameSingular}_id")->references('id')->on($table1NamePlural)->onDelete('cascade');
    // $table->integer("{$table2NameSingular}_id")->unsigned();
    // $table->foreign("{$table2NameSingular}_id")->references('id')->on($table2NamePlural)->onDelete('cascade');
    // $table->index(["{$table2NameSingular}_id", "{$table1NameSingular}_id"]);
});
```

A few CRUD controllers require that your model have a field in the database with a specific name: `published`, `publish_start_date`, `publish_end_date`, `public`, and `position`, so stick with those column names if you are going to use publication status, timeframe and reorderable listings.

### Models

Set your fillables to prevent mass-assignement. This is very important, as we use `request()->all()` in the module controller.

For fields that should always be saved as null in the database when not sent by the form, use the `nullable` array.

For fields that should always be saved to false in the database when not sent by the form, use the `checkboxes` array. The `published` field is a good example.

Depending on the features you need on your model, include the available traits and configure their respective options:

- HasPosition: implement the `A17\Twill\Models\Behaviors\Sortable` interface and add a position field to your fillables.

- HasTranslation: add translated fields in the `translatedAttributes` array and in the `fillable` array of the generated translatable model in `App/Models/Translations` (always keep the `active` and `locale` fields).

When using Twill's `HasTranslation` trait on a model, you are actually using the popular `dimsav/translatable` package. A default configuration will be automatically published to your `config` directory when you run the `twill:install` command.

To setup your list of available languages for translated fields, modify the `locales` array in `config/translatable.php`, using ISO 639-1 two-letter languages codes as in the following example:

```php
<?php

return [
    'locales' => [
        'en',
        'fr',
    ],
    ...
];
```

- HasSlug: specify the field(s) that is going to be used to create the slug in the `slugAttributes` array

- HasMedias: add the `mediasParams` configuration array:

```php
<?php

public $mediasParams = [
    'cover' => [ // role name
        'default' => [ // crop name
            [
                'name' => 'default', // ratio name, same as crop name if single
                'ratio' => 16 / 9, // ratio as a fraction or number
            ],
        ],
        'mobile' => [
            [
                'name' => 'landscape', // ratio name, multiple allowed
                'ratio' => 16 / 9,
            ],
            [
                'name' => 'portrait', // ratio name, multiple allowed
                'ratio' => 3 / 4,
            ],
        ],
    ],
    '...' => [ // another role
        ... // with crops
    ]
];
```

- HasFiles: add the `filesParams` configuration array

```php
<?php

public $filesParams = ['file_role', ...]; // a list of file roles
```

- HasRevisions: no options


### Repositories

Depending on the model feature, include one or multiple of these traits: `HandleTranslations`, `HandleSlugs`, `HandleMedias`, `HandleFiles`, `HandleRevisions`, `HandleBlocks`, `HandleRepeaters`, `HandleTags`.

Repositories allows you to modify the default behavior of your models by providing some entry points in the form of methods that you might implement:

- for filtering:

```php
<?php

// implement the filter method
public function filter($query, array $scopes = []) {

    // and use the following helpers

    // add a where like clause
    $this->addLikeFilterScope($query, $scopes, 'field_in_scope');

    // add orWhereHas clauses
    $this->searchIn($query, $scopes, 'field_in_scope', ['field1', 'field2', 'field3']);

    // add a whereHas clause
    $this->addRelationFilterScope($query, $scopes, 'field_in_scope', 'relationName');

    // or just go manually with the $query object
    if (isset($scopes['field_in_scope'])) {
      $query->orWhereHas('relationName', function ($query) use ($scopes) {
          $query->where('field', 'like', '%' . $scopes['field_in_scope'] . '%');
      });
    }

    // don't forget to call the parent filter function
    return parent::filter($query, $scopes);
}
```

- for custom ordering:

```php
<?php

// implement the order method
public function order($query, array $orders = []) {
    // don't forget to call the parent order function
    return parent::order($query, $orders);
}
```

- for custom form fieds

```php
<?php

// implement the getFormFields method
public function getFormFields($object) {
    // don't forget to call the parent getFormFields function
    $fields = parent::getFormFields($object);

    // get fields for a browser
    $fields['browsers']['relationName'] = $this->getFormFieldsForBrowser($object, 'relationName');

    // get fields for a repeater
    $fields = $this->getFormFieldsForRepeater($object, $fields, 'relationName', 'ModelName', 'repeaterItemName');

    // return fields
    return $fields
}

```

- for custom field preparation before create action


```php
<?php

// implement the prepareFieldsBeforeCreate method
public function prepareFieldsBeforeCreate($fields) {
    // don't forget to call the parent prepareFieldsBeforeCreate function
    return parent::prepareFieldsBeforeCreate($fields);
}

```

- for custom field preparation before save action


```php
<?php

// implement the prepareFieldsBeforeSave method
public function prepareFieldsBeforeSave($object, $fields) {
    // don't forget to call the parent prepareFieldsBeforeSave function
    return parent:: prepareFieldsBeforeSave($object, $fields);
}

```

- for after save actions (like attaching a relationship)

```php
<?php

// implement the afterSave method
public function afterSave($object, $fields) {
    // for exemple, to sync a many to many relationship
    $this->updateMultiSelect($object, $fields, 'relationName');

    // which will simply run the following for you
    $object->relationName()->sync($fields['relationName'] ?? []);

    // or, to save a oneToMany relationship
    $this->updateOneToMany($object, $fields, 'relationName', 'formFieldName', 'relationAttribute')

    // or, to save a belongToMany relationship used with the browser field
    $this->updateBrowser($object, $fields, 'relationName');

    // or, to save a hasMany relationship used with the repeater field
    $this->updateRepeater($object, $fields, 'relationName', 'ModelName', 'repeaterItemName');

    // or, to save a belongToMany relationship used with the repeater field
    $this->updateRepeaterMany($object, $fields, 'relationName', false);

    parent::afterSave($object, $fields);
}

```

- for hydrating the model for preview of revisions

```php
<?php

// implement the hydrate method
public function hydrate($object, $fields)
{
    // for exemple, to hydrate a belongToMany relationship used with the browser field
    $this->hydrateBrowser($object, $fields, 'relationName');

    // or a multiselect
    $this->hydrateMultiSelect($object, $fields, 'relationName');

    // or a repeater
    $this->hydrateRepeater($object, $fields, 'relationName');

    return parent::hydrate($object, $fields);
}
```

### Controllers

```php
<?php

    protected $moduleName = 'yourModuleName';

    /*
     * Options of the index view
     */
    protected $indexOptions = [
        'create' => true,
        'edit' => true,
        'publish' => true,
        'bulkPublish' => true,
        'feature' => false,
        'bulkFeature' => false,
        'restore' => true,
        'bulkRestore' => true,
        'delete' => true,
        'bulkDelete' => true,
        'reorder' => false,
        'permalink' => true,
        'bulkEdit' => true,
        'editInModal' => false,
    ];

    /*
     * Key of the index column to use as title/name/anythingelse column
     * This will be the first column in the listing and will have a link to the form
     */
    protected $titleColumnKey = 'title';

    /*
     * Available columns of the index view
     */
    protected $indexColumns = [
        'image' => [
            'thumb' => true, // image column
            'variant' => [
                'role' => 'cover',
                'crop' => 'default',
            ],
        ],
        'title' => [ // field column
            'title' => 'Title',
            'field' => 'title',
        ],
        'subtitle' => [
            'title' => 'Subtitle',
            'field' => 'subtitle',
            'sort' => true, // column is sortable
            'visible' => false, // will be available from the columns settings dropdown
        ],
        'relationName' => [ // relation column
            // Take a look at the example in the next section fot the implementation of the sort
            'title' => 'Relation name',
            'sort' => true,
            'relationship' => 'relationName',
            'field' => 'relationFieldToDisplay'
        ],
        'presenterMethodField' => [ // presenter column
            'title' => 'Field title',
            'field' => 'presenterMethod',
            'present' => true,
        ]
    ];

    /*
     * Columns of the browser view for this module when browsed from another module
     * using a browser form field
     */
    protected $browserColumns = [
        'title' => [
            'title' => 'Title',
            'field' => 'title',
        ],
    ];

    /*
     * Relations to eager load for the index view
     */
    protected $indexWith = [];

    /*
     * Relations to eager load for the form view
     * Add relationship used in multiselect and resource form fields
     */
    protected $formWith = [];

    /*
     * Relation count to eager load for the form view
     */
    protected $formWithCount = [];

    /*
     * Filters mapping ('filterName' => 'filterColumn')
     * You can associate items list to filters by having a filterNameList key in the indexData array
     * For example, 'category' => 'category_id' and 'categoryList' => app(CategoryRepository::class)->listAll()
     */
    protected $filters = [];

    /*
     * Add anything you would like to have available in your module's index view
     */
    protected function indexData($request)
    {
        return [];
    }

    /*
     * Add anything you would like to have available in your module's form view
     * For example, relationship lists for multiselect form fields
     */
    protected function formData($request)
    {
        return [];
    }

    // Optional, if the automatic way is not working for you (default is ucfirst(str_singular($moduleName)))
    protected $modelName = 'model';

    // Optional, to specify a different feature field name than the default 'featured'
    protected $featureField = 'featured';

    // Optional, specify number of items per page in the listing view (-1 to disable pagination)
    protected $perPage = 20;

    // Optional, specify the default listing order
    protected $defaultOrders = ['title' => 'asc'];

    // Optional, specify the default listing filters
    protected $defaultFilters = ['search' => 'title|search'];
```

You can also override all actions and internal functions, checkout the ModuleController source in `A17\Twill\Http\Controllers\Admin\ModuleController`.

#### Example Sort by Relationship Field.

Let's say we have a controller with certain fields displayed:
File: `app/Http/Controllers/Admin/PlayController.php`

```php
    protected $indexColumns = [
        'image' => [
            'thumb' => true, // image column
            'variant' => [
                'role' => 'featured',
                'crop' => 'default',
            ],
        ],
        'title' => [ // field column
            'title' => 'Title',
            'field' => 'title',
        ],
        'festivals' => [ // relation column
            'title' => 'Festival',
            'sort' => true,
            'relationship' => 'festivals',
            'field' => 'title'
        ],
    ];
```

For creating the Sorting mechanism for the relationship we need to overwrite the order method on the proper repository.
In there we verify for the parameter sent which per convention should be *relationship + field* in this case `festivalsTitle`.
Once applied we remove that parameter to avoid the application crash due to not being able to find the field on the table.

File: `app/Repositories/PlayRepository.php`

```php
  ...
  public function order($query, array $orders = []) {

      if (array_key_exists('festivalsTitle', $orders)){
          $sort_method = $orders['festivalsTitle'];
          //Remove the UNEXISTING column from the orders array
          unset($orders['festivalsTitle']);
          $query = $query->orderByFestival($sort_method);
      }
      // don't forget to call the parent order function
      return parent::order($query, $orders);
  }
  ...
```

Then add the custom `sort` scope into your Model, it should be something like this:
File: `app/Models/Play.php`

```php
    public function scopeOrderByFestival($query, $sort_method = 'ASC') {
        return $query
            ->leftJoin('festivals', 'plays.section_id', '=', 'festivals.id')
            ->select('plays.*', 'festivals.id', 'festivals.title')
            ->orderBy('festivals.title', $sort_method);
    }
```


### Form Requests
Classic Laravel 5 [form request validation](https://laravel.com/docs/5.5/validation#form-request-validation).

Once you generated the module using Twill's CLI module generator, it will also prepare the `App/Http/Requests/Admin/ModuleNameRequest.php` for you to use.
You can choose to use different rules for creation and update by implementing the following 2 functions instead of the classic `rules` one:

```php
<?php

public function rulesForCreate()
{
    return [];
}

public function rulesForUpdate()
{
    return [];
}
```

There is also an helper to define rules for translated fields without having to deal with each locales:

```php
<?php

$this->rulesForTranslatedFields([
 // regular rules
], [
  // translated fields rules with just the field name like regular rules
]);
```

There is also an helper to define validation messages for translated fields:

```php
<?php

$this->messagesForTranslatedFields([
 // regular messages
], [
  // translated fields messages
]);
```

Once you defined the rules in this file, the UI will show the corresponding validation error state or message next to the corresponding form field.

### Routes

A router macro is available to create module routes quicker:
```php
<?php

Route::module('yourModulePluralName');

// You can add an array of only/except action names as a second parameter
// By default, the following routes are created : 'reorder', 'publish', 'browser', 'bucket', 'feature', 'restore', 'bulkFeature', 'bulkPublish', 'bulkDelete', 'bulkRestore'
Route::module('yourModulePluralName', ['except' => ['reorder', 'feature', 'bucket', 'browser']])

// You can add an array of only/except action names for the resource controller as a third parameter
// By default, the following routes are created : 'index', 'store', 'show', 'edit', 'update', 'destroy'
Route::module('yourModulePluralName', [], ['only' => ['index', 'edit', 'store', 'destroy']])

// The last optional parameter disable the resource controller actions on the module
Route::module('yourPluralModuleName', [], [], false)
```

### Form fields


Wrap them into the following in your module `form` view (`resources/views/admin/moduleName/form.blade.php`):

```php
@extends('twill::layouts.form')
@section('contentFields')
    @formField('...', [...])
    ...
@stop
```

The idea of the `contentFields` section is to contain the most important fields and the block editor as the last field.

If you have attributes, relationships, extra images, file attachments or repeaters, you'll want to add a `fieldsets` section after the `contentFields` section and use the `a17-fieldset` Vue component to create new ones like in the following example:

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
    <a17-fieldset title="Attributes" id="attributes">
        @formField('...', [...])
        ...
    </a17-fieldset>
@stop
```

The additional fieldsets array passed to the form layout will display a sticky navigation of your fieldset on scroll.
You can also rename the content section by passing a `contentFieldsetLabel` property to the layout.

#### Input
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

#### WYSIWYG
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
WYSIWYG field is based on [Quill](https://quilljs.com/) Rich Text Editor. 

You can add all [toolbar options](https://quilljs.com/docs/modules/toolbar/) from Quill with the `toolbarOptions` key.

For example, this configuration will render a `wysiwyg` field with almost all features from Quill and Snow theme.

```
 @formField('wysiwyg', [
    'name' => 'case_study',
    'label' => 'Case study text',
    'toolbarOptions' => [ 
      ["font" => ["serif", "sans-serif", "monospace"]],
      ['header' => [2, 3, 4, 5, 6, false]],
      'bold',
      'italic',
      'underline',
      'strike',
      ["color" => []],
      ["background" => []],
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
      'image',
      'video',
      "clean",
    ],
    'placeholder' => 'Case study text',
    'maxlength' => 200,
    'editSource' => true,
    'note' => 'Hint message`',
 ])
```

Note that Quill outputs CSS classes in the HTML for certain toolbar modules (indent, font, align, etc.), and that the image module is not integrated with Twill's media library. It outputs the base64 representation of the uploaded image. It is not a recommended way of using and storing images, prefer using one or multiple `medias` form fields or blocks fields for flexible content. This will give you greater control over your frontend output.

#### Medias
![screenshot](/docs/_media/medias.png)

```php
@formField('medias', [
    'name' => 'cover',
    'label' => 'Cover image',
    'note' => 'Minimum image width 1300px'
])

@formField('medias', [
    'name' => 'slideshow',
    'label' => 'Slideshow',
    'max' => 5,
    'note' => 'Minimum image width: 1500px'
])
```

Right after declaring the media formField in the blade template file, you still need to do a few things to make it works properly.

If the formField is in a static content form, you have to include the `HasMedias` Trait in your module's [Model](https://twill.io/docs/#models) and inlcude `HandleMedias` in your module's [Repository](https://twill.io/docs/#repositories), in addition, you have to uncomment the `$mediasParams` section in your Model file to let the model know about fields you'd like to save from the form.

Learn more about how Twill's media configurations work at [Model](https://twill.io/docs/#models), [Repository](https://twill.io/docs/#repositories), [Media Library Role & Crop Params](https://twill.io/docs/#image-rendering-service)

If the formField is used inside a block, you need to define the `mediasParams` at `config/twill.php` under `crops` key, and you are good to go. You could checkout [Twill Default Configuration](https://twill.io/docs/#default-configuration) and [Rendering Blocks](https://twill.io/docs/#rendering-blocks) for references.

If you need medias fields to be translatable (ie. publishers can select different images for each locale), set the `twill.media_library.translated_form_fields` configuration value to `true`.

#### Datepicker
![screenshot](/docs/_media/datepicker.png)

```php
@formField('date_picker', [
    'name' => 'event_date',
    'label' => 'Event date',
    'minDate' => '2017-09-10 12:00',
    'maxDate' => '2017-12-10 12:00'
])
```

#### Select
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

#### Select unpacked
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

#### Multi select
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

#### Block editor
![screenshot](/docs/_media/blockeditor.png)

```php
@formField('block_editor', [
    'blocks' => ['title', 'quote', 'text', 'image', 'grid', 'test', 'publications', 'news']
])
```

#### Repeater
![screenshot](/docs/_media/repeater.png)

```php
<a17-fieldset title="Videos" id="videos" :open="true">
    @formField('repeater', ['type' => 'video'])
</a17-fieldset>
```

#### Browser
![screenshot](/docs/_media/browser.png)

```php
<a17-fieldset title="Related" id="related" :open="true">
    @formField('browser', [
        'label' => 'Publications',
        'max' => 4,
        'name' => 'publications',
        'moduleName' => 'publications'
    ])
</a17-fieldset>
```

#### Files
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

If you are using the file formField in a block, you have to define the `files` key in `config/twill.php` and you are all set, put it under `block_editor` key and at the same level as `crops` key:
```php
return [
    'block_editor' => [
        'crops' => [
            ...
        ],
        'files' => ['file_role1', 'file_role2', ...]
    ]
```

#### Map
![screenshot](/docs/_media/map.png)

```php
@formField('map', [
    'name' => 'location',
    'label' => 'Location',
    'showMap' => false,
])
```

This field requires that you provide a `GOOGLE_MAPS_API_KEY` variable in your .env file.

#### Color

```php
@formField('color', [
    'name' => 'main-color',
    'label' => 'Main color'
])
```

#### Single checkbox

```php
@formField('checkbox', [
    'name' => 'featured',
    'label' => 'Featured'
])
```

#### Multiple checkboxes (multi select as checkboxes)

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

#### Radios

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

### Revisions and previewing

When using the `HasRevisions` trait, Twill's UI gives publishers the ability to preview their changes without saving, as well as to preview and compare old revisions.

If you are implementing your site using Laravel routing and Blade templating (ie. traditional server side rendering), you can follow Twill's convention of creating frontend views at `resources/views/site` and naming them according to their corresponding CRUD module name. When publishers try to preview their changes, Twill will render your frontend view within an iframe, passing the previewed record with it's unsaved changes to your view in the `$item` variable.

If you want to provide Twill with a custom frontend views path, use the `frontend` configuration array of your `config/twill.php` file:

```php
return [
    'frontend' => [
        'views_path' => 'site',
    ],
    ...
];
```

If you named your frontend view differently than the name of its corresponding module, you can use the $previewView class property of your module's controller:

```php
<?php
...

class ProjectController extends ModuleController
{
    protected $moduleName = 'projects';

    protected $previewView = 'custom-view-name';
    ...
}
```

If you want to provide the previewed view with extra variables or simply to rename the `$item` variable, you can implement the `previewData` function in your module's admin controller:

```php
<?php
...
protected function previewData($item)
{
    return [
        'project' => $item,
        'setting_name' => $settingRepository->byKey('setting_name')
    ];
}
```

### Nested Module

To create a nested module with parent/child relationships, you should include the `laravel-nestedset` package to your application.

To install the package: `composer require kalnoy/nestedset`

Then add nested set columns to your database table:

For Laravel 5.5 and above users:

```php
Schema::create('pages', function (Blueprint $table) {
    ...
    $table->nestedSet();
});

// To drop columns
Schema::table('pages', function (Blueprint $table) {
    $table->dropNestedSet();
});
```

For prior Laravel Versions:

```php
...
use Kalnoy\Nestedset\NestedSet;

Schema::create('pages', function (Blueprint $table) {
    ...
    NestedSet::columns($table);
});

// To drop columns
Schema::table('pages', function (Blueprint $table) {
    NestedSet::dropColumns($table);
});
```

Your model should use the `Kalnoy\Nestedset\NodeTrait` trait to enable nested sets, as well as the `HasPosition` trait and some helper functions to save a new tree organisation from Twill's drag and drop UI:

```php
use A17\Twill\Models\Behaviors\HasPosition;
use Kalnoy\Nestedset\NodeTrait;
...

class Page extends Model {
    use HasPostion, NodeTrait;
    ...
    public static function saveTreeFromIds($nodesArray)
    {
        $parentNodes = self::find(array_pluck($nodesArray, 'id'));

        if (is_array($nodesArray)) {
            $position = 1;
            foreach ($nodesArray as $nodeArray) {
                $node = $parentNodes->where('id', $nodeArray['id'])->first();
                $node->position = $position++;
                $node->saveAsRoot();
            }
        }

        $parentNodes = self::find(array_pluck($nodesArray, 'id'));

        self::rebuildTree($nodesArray, $parentNodes);
    }

    public static function rebuildTree($nodesArray, $parentNodes)
    {
        if (is_array($nodesArray)) {
            foreach ($nodesArray as $nodeArray) {
                $parent = $parentNodes->where('id', $nodeArray['id'])->first();
                if (isset($nodeArray['children']) && is_array($nodeArray['children'])) {
                    $position = 1;
                    $nodes = self::find(array_pluck($nodeArray['children'], 'id'));
                    foreach ($nodeArray['children'] as $child) {
                        //append the children to their (old/new)parents
                        $descendant = $nodes->where('id', $child['id'])->first();
                        $descendant->position = $position++;
                        $descendant->parent_id = $parent->id;
                        $descendant->save();
                        self::rebuildTree($nodeArray['children'], $nodes);
                    }
                }
            }
        }
    }
}
```

From your module's repository, you'll need to override the `setNewOrder` function:

```php
public function setNewOrder($ids)
{
    DB::transaction(function () use ($ids) {
        Page::saveTreeFromIds($ids);
    }, 3);
}
```

If you expect your users to create a lot of records, you'll want to move this operation into a queued job.

Finally, to enable Twill's nested listing UI, you'll need to do the following in your module's controller:

```php
protected $indexOptions = [
    'reorder' => true,
];

protected function indexData($request)
{
    return [
        'nested' => true,
        'nestedDepth' => 2, // this controls the allowed depth in UI
    ];
}

protected function transformIndexItems($items)
{
    return $items->toTree();
}

protected function indexItemData($item)
{
    return ($item->children ? [
        'children' => $this->getIndexTableData($item->children),
    ] : []);
}
```

When using a browser to browse a nested module, if you expect to select children as well as parents, you will need to add the following function to your module's controller:
```
protected function getBrowserItems($scopes = [])
{
    return $this->repository->get(
        $this->indexWith,
        $scopes,
        $this->orderScope(),
        request('offset') ?? $this->perPage ?? 50,
        true
    );
}
```
