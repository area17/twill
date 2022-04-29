---
pageClass: twill-doc
---

# Nested Modules

Out of the box, Twill supports 2 kinds of nested modules: [self-nested](#self-nested-modules) and [parent-child](#parent-child-modules).

## Self-nested modules

Self-nested modules allow items to be nested within other items of the same module (e.g. Pages can contain other Pages):

![self-nested module](/docs/_media/nested-module.png)

### Creating self-nested modules

You can enable nesting when creating a new module with the `--hasNesting` or `-N` option:

```
php artisan twill:make:module -N pages
```

This will prefill some options and methods in your module's controller and use the supporting traits on your model and repository.

This feature requires the `laravel-nestedset` package, which can be installed via composer:

```
composer require kalnoy/nestedset
```

### Working with self-nested items

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

## Parent-child modules

Parent-child modules are 2 distinct modules, where items of the child module are attached to items of the parent module (e.g. Issues can contain Articles):

![parent-child modules](/docs/_media/nested-parent-index.png)

Items of the child module can't be created independently.

### Creating parent-child modules

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

Add the parent module to `config/twill-navigation.php`:

```php
return [
    'issues' => [
        'title' => 'Issues',
        'module' => true,
    ],
];
```

Then, rename and move the `articles/` views folder inside of the parent `issues/` folder:
```
resources/views/admin/
└── issues
    ├── articles
    │   └── form.blade.php
    └── form.blade.php
...
```

### Using breadcrumbs for easier navigation

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
