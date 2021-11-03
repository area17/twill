## CRUD modules
Twill core functionality is the ability to setup what we call modules. A module is a set of files that define a content model and its associated business logic in your application. Modules can be configured to enable several features for publishers, from the ability to translate content, to the ability to attach images and create a more complex data structure in your records.

### CLI Generator
You can generate all the files needed in your application to create a new CRUD module using Twill's Artisan generator:

```bash
php artisan twill:module moduleName
```

The command accepts several options:
- `--hasBlocks (-B)`, to use the block editor on your module form
- `--hasTranslation (-T)`, to add content in multiple languages
- `--hasSlug (-S)`, to generate slugs based on one or multiple fields in your model
- `--hasMedias (-M)`, to attach images to your records
- `--hasFiles (-F)`, to attach files to your records
- `--hasPosition (-P)`, to allow manually reordering of records in the listing screen
- `--hasRevisions(-R)`, to allow comparing and restoring past revisions of records
- `--hasNesting(-N)`, to enable nested items in the module listing (see [Nested Module](#nested-module))

The `twill:module` command will generate a migration file, a model, a repository, a controller, a form request object and a form view.

Add the route to your admin routes file(`routes/admin.php`).

```php
<?php

Route::module('moduleName');
```

Setup a new CMS navigation item in `config/twill-navigation.php`.

```php
return [
    ...
    'moduleName' => [
        'title'     => 'Module name',
        'module'    => true
    ]
    ...
]
```

With that in place, after migrating the database using `php artisan migrate`, you should be able to start creating content. By default, a module only have a title and a description, the ability to be published, and any other feature you added through the CLI generator.

If you provided the `hasBlocks` option, you will be able to use the `block_editor` form field in the form of that module.

If you provided the `hasTranslation` option, and have multiple languages specified in your `translatable.php` configuration file, the UI will react automatically and allow publishers to translate content and manage publication at the language level. 

If you provided the `hasSlug` option, slugs will automatically be generated from the title field.

If you provided the `hasMedias` or `hasFiles` option, you will be able to respectively add several `medias` or `files` form fields to the form of that module.

If you provided the `hasPosition` option, publishers will be able to manually order  records from the module's listing screen (after enabling the `reorder` option in the module's controller `indexOptions` array).

If you provided the `hasRevisions` option, each form submission will create a new revision in your database so that publishers can compare and restore them in the CMS UI.

Depending on the depth of your module in your navigation, you'll need to wrap your route declaration in one or multiple nested route groups.

You can setup your index options and columns in the generated controller if needed.

### Migrations
Twill's generated migrations are standard Laravel migrations, enhanced with helpers to create the default fields any CRUD module will use:
```php
<?php

// main table, holds all non translated fields
Schema::create('table_name_plural', function (Blueprint $table) {
    createDefaultTableFields($table);
    // will add the following inscructions to your migration file
    // $table->increments('id');
    // $table->softDeletes();
    // $table->timestamps();
    // $table->boolean('published');
});

// translation table, holds translated fields
Schema::create('table_name_singular_translations', function (Blueprint $table) {
    createDefaultTranslationsTableFields($table, 'tableNameSingular');
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
    createDefaultSlugsTableFields($table, 'tableNameSingular');
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
    createDefaultRelationshipTableFields($table, $table1NameSingular, $table2NameSingular);
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

For fields that should default as null in the database when not sent by the form, use the `nullable` array.

For fields that should default to false in the database when not sent by the form, use the `checkboxes` array.

Depending upon the Twill features you need on your model, include the related traits and configure their respective options:

- HasPosition: implement the `A17\Twill\Models\Behaviors\Sortable` interface and add a position field to your fillables.

- HasTranslation: add translated fields in the `translatedAttributes` array.

Twill's `HasTranslation` trait is a wrapper around the popular `astronomic/laravel-translatable` package. A default configuration will be automatically published to your `config` directory when you run the `twill:install` command.

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

- HasSlug: specify the field(s) used to create the slug in the `slugAttributes` array

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

- for custom form fields

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
     * The static permalink base to your module. Defaults to /yourModuleName
     * Set to '' if your module's permalinks are directly off the root, like in a Pages module, for example 
     */
    protected $permalinkBase = 'yourModuleName';

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
        'forceDelete' => true,
        'bulkForceDelete' => true,
        'delete' => true,
        'duplicate' => false,
        'bulkDelete' => true,
        'reorder' => false,
        'permalink' => true,
        'bulkEdit' => true,
        'editInModal' => false,
        'skipCreateModal' => false,
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
    // If you are implementing Sortable, this parameter is ignored given reordering is not implemented
    // along with pagination.
    protected $perPage = 20;

    // Optional, specify the default listing order
    protected $defaultOrders = ['title' => 'asc'];

    // Optional, specify the default listing filters
    protected $defaultFilters = ['search' => 'title|search'];
```

You can also override all actions and internal functions, checkout the ModuleController source in `A17\Twill\Http\Controllers\Admin\ModuleController`.

#### Example: sorting by a relationship field

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

To order by the relationship we need to overwrite the order method in the module's repository.

File: `app/Repositories/PlayRepository.php`
```php
  ...
  public function order($query, array $orders = []) {

      if (array_key_exists('festivalsTitle', $orders)){
          $sort_method = $orders['festivalsTitle'];
          // remove the unexisting column from the orders array
          unset($orders['festivalsTitle']);
          $query = $query->orderByFestival($sort_method);
      }
      // don't forget to call the parent order function
      return parent::order($query, $orders);
  }
  ...
```

Then, add a custom `sort` scope to your model, it could be something like this:

File: `app/Models/Play.php`
```php
    public function scopeOrderByFestival($query, $sort_method = 'ASC') {
        return $query
            ->leftJoin('festivals', 'plays.section_id', '=', 'festivals.id')
            ->select('plays.*', 'festivals.id', 'festivals.title')
            ->orderBy('festivals.title', $sort_method);
    }
```

#### Additional table actions

You can override the `additionalTableActions()` method to add custom actions in your module's listing view:

File: `app/Http/Controllers/Admin/NewsletterController.php`
```php
    public function additionalTableActions()
    {
        return [
            'exportAction' => [ // Action name.
                'name' => 'Export Newsletter List', // Button action title.
                'variant' => 'primary', // Button style variant. Available variants; primary, secondary, action, editor, validate, aslink, aslink-grey, warning, ghost, outline, tertiary
                'size' => 'small', // Button size. Available sizes; small
                'link' => route('newsletter.export'), // Button action link.
                'target' => '', // Leave it blank for self.
                'type' => 'a', // Leave it blank for "button".
            ]
        ];
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
Route::module('yourModulePluralName', ['except' => ['reorder', 'feature', 'bucket', 'browser']]);

// You can add an array of only/except action names for the resource controller as a third parameter
// By default, the following routes are created : 'index', 'store', 'show', 'edit', 'update', 'destroy'
Route::module('yourModulePluralName', [], ['only' => ['index', 'edit', 'store', 'destroy']]);

// The last optional parameter disable the resource controller actions on the module
Route::module('yourPluralModuleName', [], [], false);
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

Module items can be visually nested within the listing view:

![screenshot](/docs/_media/nested-module.png)

You can enable nesting when creating a new module with the `--hasNesting` or `-N` option:

```
php artisan twill:module:make -N pages
```

This will prefill some options and methods in your module's controller and use the supporting traits on your model and repository.

This feature requires the `laravel-nestedset` package, which can be installed via composer:

```
composer require kalnoy/nestedset
```

#### Working with nested items

A few accessors and methods are available to work with nested item slugs:

```php
// Get the combined slug for all ancestors of an item in the current locale:
$slug = $item->ancestorsSlug;

// for a specific locale:
$slug = $item->getAncestorsSlug($lang);

// Get the combined slug for an item including all ancestors:
$slug = $item->nestedSlug;

// for a specific locale:
$slug = $item->getNestedSlug($lang);
```

To include all ancestor slugs in the permalink of an item in the CMS, you can dynamically set the `$permalinkBase` property from the `form()` method of your module controller:

```php
class PageController extends ModuleController
{
    //...

    protected function form($id, $item = null)
    {
        $item = $this->repository->getById($id, $this->formWith, $this->formWithCount);

        $this->permalinkBase = $item->ancestorsSlug;

        return parent::form($id, $item);
    }
}
```

To implement routing for nested items, you can combine the `forNestedSlug()` method from `HandleNesting` with a wildcard route parameter:

```php
// file: routes/web.php

Route::get('{slug}', function ($slug) {
    $page = app(PageRepository::class)->forNestedSlug($slug);

    abort_unless($page, 404);

    return view('site.page', ['page' => $page]);
})->where('slug', '.*');
```

For more information on how to work with nested items in your application, you can refer to the 
[laravel-nestedset package documentation](https://github.com/lazychaser/laravel-nestedset#retrieving-nodes).

#### Parent-child modules

Parent-child modules are 2 distinct modules, where items of the child module are attached to items of the parent modules (e.g. Issues can contain Articles):

![parent-child modules](/docs/_media/nested-parent-index.png)

Items of the child module can't be created independently.

#### Creating parent-child modules

We'll use the `slug` and `position` features in this example but you can customize as needed:

```
php artisan twill:module issues -SP
php artisan twill:module issueArticles -SP
```

Add the `issue_id` foreign key to the child module's migration:

```php
class CreateIssueArticlesTables extends Migration
{
    public function up()
    {
        Schema::create('issue_articles', function (Blueprint $table) {
            // ...
            $table->unsignedBigInteger('issue_id')->nullable();
            $table->foreign('issue_id')->references('id')->on('issues');
        });
        
        // ...
    }
}
```

Run the migrations:

```
php artisan migrate
```

Update the child model. Add the `issue_id` fillable and the relationship to the parent model:

```php
class IssueArticle extends Model implements Sortable
{
    // ...

    protected $fillable = [
        // ...
        'issue_id',
    ];
    
    public function issue()
    {
        return $this->belongsTo(Issue::class);
    }
}
```

Update the parent model. Add the relationship to the child model:

```php
class Issue extends Model implements Sortable
{
    // ...

    public function articles()
    {
        return $this->hasMany(IssueArticle::class);
    }
}
```

Update the child controller. Set the `$moduleName` and `$modelName` properties, then override the `getParentModuleForeignKey()` method:

```php
class IssueArticleController extends BaseModuleController
{
    protected $moduleName = 'issues.articles';

    protected $modelName = 'IssueArticle';

    protected function getParentModuleForeignKey()
    {
        return 'issue_id';
    }
}
```

Update the parent controller. Set the `$indexColumns` property to include a new `Articles` column. This will be a link to the child module items, for each parent.

```php
class IssueController extends BaseModuleController
{
    protected $moduleName = 'issues';

    protected $indexColumns = [
        'title' => [
            'title' => 'Title',
            'field' => 'title',
        ],
        'articles' => [
            'title' => 'Articles',
            'nested' => 'articles',
        ],
    ];
}
```

Add both modules to `routes/admin.php`:

```php
Route::module('issues');
Route::module('issues.articles');
```

Then, add the parent module to `config/twill-navigation.php`:

```php
return [
    'issues' => [
        'title' => 'Issues',
        'module' => true,
    ],
];
```

#### Using breadcrumbs for easier navigation

In the child module controller, override the `indexData()` method to add the breadcrumbs to the index view:

```php
class IssueArticleController extends BaseModuleController
{
    // ...

    protected function indexData($request)
    {
        $issue = app(IssueRepository::class)->getById(request('issue'));

        return [
            'breadcrumb' => [
                [
                    'label' => 'Issues',
                    'url' => moduleRoute('issues', '', 'index'),
                ],
                [
                    'label' => $issue->title,
                    'url' => moduleRoute('issues', '', 'edit', $issue->id),
                ],
                [
                    'label' => 'Articles',
                ],
            ],
        ];
    }
}
```

![child module index](/docs/_media/nested-child-index.png)

<br>

Then, override the `formData()` method to do the same in the form view:

```php
    protected function formData($request)
    {
        $issue = app(IssueRepository::class)->getById(request('issue'));

        return [
            'breadcrumb' => [
                [
                    'label' => 'Issues',
                    'url' => moduleRoute('issues', '', 'index'),
                ],
                [
                    'label' => $issue->title,
                    'url' => moduleRoute('issues', '', 'edit', $issue->id),
                ],
                [
                    'label' => 'Articles',
                    'url' => moduleRoute('issues.articles', '', 'index'),
                ],
                [
                    'label' => 'Edit',
                ],
            ],
        ];
    }
```

![nested child form](/docs/_media/nested-child-form.png)
