# Nested Modules

Out of the box, Twill supports 2 kinds of nested modules: [self-nested](#content-self-nested-modules) and [parent-child](#content-parent-child-modules).

## Self-nested modules

Self-nested modules allow items to be nested within other items of the same module (e.g. Pages can contain other Pages):

![self-nested module](/assets/nested-module.png)

### Creating self-nested modules

You can enable nesting when creating a new module with the `--hasNesting` or `-N` option:

```
php artisan twill:make:module -N pages
```

This will prefill some options and methods in your module's controller and use the supporting traits on your model and repository.

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
class PageController extends NestedModuleController
{
    //...

    protected function form(?int $id, TwillModelContract $item = null): array
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

### Setting a maximum nested depth

You can also define the maximum depth allowed for the module changing the following:
```php 
protected $nestedItemsDepth = 1;
```
Note: a depth of 1 means parent and child.

### Working with browser fields

By default only a parent item will be visible to the browser field. If you want to show child items when browsing for the module you can set `$showOnlyParentItemsInBrowsers` to false:
```php
protected $showOnlyParentItemsInBrowsers = false; // default is true
```

## Parent-child modules

Parent-child modules are 2 distinct modules, where items of the child module are attached to items of the parent module (e.g. Issues can contain Articles):

![parent-child modules](/assets/nested-parent-index.png)

Items of the child module can't be created independently.

### Creating parent-child modules

We'll use the `slug` and `position` features in this example but you can customize as needed:

```
php artisan twill:make:module issues -SP
php artisan twill:make:module issueArticles -SP --parentModel=Issue 
```

Add the `issue_id` foreign key to the child module's migration:

```php
class CreateIssueArticlesTables extends Migration
{
    public function up()
    {
        Schema::create('issue_articles', function (Blueprint $table) {
            // ...
            $table->foreignIdFor(Issue::class);
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
use A17\Twill\Services\Breadcrumbs\NestedBreadcrumbs;

class IssueArticleController extends BaseModuleController
{
    protected $moduleName = 'issues.articles';
    protected $modelName = 'IssueArticle';

    protected function setUpController(): void
    {
        if (request('issue')) {
            $this->setBreadcrumbs(
                NestedBreadcrumbs::make()
                    ->forParent(
                        parentModule: 'issues',
                        module: $this->moduleName,
                        activeParentId: request('issue'),
                        repository: \App\Repositories\IssueRepository::class
                    )
                    ->label('Article')
            );
        }
    }
}

```

Update the parent controller. Set the `$indexColumns` property to include a new `Articles` column. This will be a link to the child module items, for each parent.

```php

use A17\Twill\Services\Listings\Columns\NestedData;
use A17\Twill\Services\Listings\TableColumns;

class IssueController extends BaseModuleController
{
    protected $moduleName = 'issues';

    protected function additionalIndexTableColumns(): TableColumns
    {
        $table = parent::additionalIndexTableColumns();

        $table->add(
            NestedData::make()->field('articles')
        );

        return $table;
    }
}
```

Add both modules to `routes/twill.php`:

```php
<?php

use A17\Twill\Facades\TwillRoutes;

TwillRoutes::module('issues');
TwillRoutes::module('issues.articles');
```

Add the parent module to `AppServiceProvider.php`:

```php
<?php

namespace App\Providers;

use A17\Twill\Facades\TwillNavigation;
use A17\Twill\View\Components\Navigation\NavigationLink;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        TwillNavigation::addLink(
            NavigationLink::make()->forModule('issues')
        );
    }
}
```

**Only when using blade forms**:

Rename and move the `articles/` views folder inside of the parent `issues/` folder:
```
resources/views/twill/
└── issues
    ├── articles
    │   └── form.blade.php
    └── form.blade.php
...
```

### Breadcrumbs

Breadcrumbs are added via the `$this->setBreadcrumbs` method, you can remove that line if you wish to not include the breadcrumbs.

![child module index](/assets/nested-child-index.png)
![nested child form](/assets/nested-child-form.png)
