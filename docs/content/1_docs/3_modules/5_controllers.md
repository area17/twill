# Controllers

Controllers take care of the main interaction between backend and the Twill frontend.

There are many things that can be set and changed.

## Controller setup

The main method you will use in a module controller is `setupController`.

In this method you can set all the options you would like to enable/disable. Keep in mind that for now we only provide methods that change the defaults. This means there might be a `disableCreate` method, but not a `enableCreate` as the latter does not change anything and would be redundant.

An example of a setupController call:

```php
<?php

namespace App\Http\Controllers\Twill;

class ProjectController extends ModuleController
{
    protected $moduleName = 'projects';
    
    public function setUpController(): void
    {
        $this->enableShowImage();
    }
}
```

Below is a list of the methods and their purpose:

### Disable defaults

- **disableCreate**: Removes the "Create" button on the listing page.
- **disableEdit**: Disables table interaction and removes edit links.
- **disableSortable**: Disables the ability to sort the table by clicking table headers.
- **disablePublish**: Removes the publish/un-publish icon on the content listing.
- **disableBulkPublish**: Removes the "publish" option from the bulk operations.
- **disableRestore**: Removes "restore" from the list item dropdown on the "Trash" content list.
- **disableBulkRestore**: Removes the "Trash" quick filter.
- **disableForceDelete**: Removes the "delete" option from the "Trash" content list.
- **disableBulkForceDelete**: Removes "restore" from the bulk operations on the "Trash" content list.
- **disableDelete**: Removes the "delete" option from the content lists.
- **disableBulkDelete**: Removes the "delete" option from the bulk operations.
- **disablePermalink**: Removes the permalink from the create/edit screens.
- **disableEditor**: Removes the editor button from the edit page.
- **disableBulkEdit**: Disables bulk operations.
- **disableIncludeScheduledInList**: Hides publish scheduling information from the content list.

### Enable defaults

- **enableSkipCreateModal**: Disables the create modal and directly forwards you to the full edit page.
- **enableFeature**: Allow to feature the content. This requires a 'featured' fillable boolean on the model.
- **enableBulkFeature**: Enables the "Feature" bulk operation.
- **enableDuplicate**: Enables the "Duplicate" option from the content lists.
- **enableReorder**: Allows to reorder the items, if this was setup on the model.
- **enableEditInModal**: Enables the function that content is edited in the create modal.
- **enableShowImage**: Shows the thumbnail of the content in the list.

### Setters

- **setModuleName**('`yourModuleName`'): Set the name of the module you are working with.
- **setFeatureField**('`fieldname`'): Set the field to use for featuring content.
- **setSearchColumns**(`['title', 'year']`): Set the columns to search in.
- **setPermalinkBase**('`example`'): The static permalink base to your module. Defaults to `setModuleName` when empty.
- **setTitleColumnKey**('`title`'): Sets the field to use as title, defaults to `title`.
- **setModelName**('`Project`'): Usually not required, but in case customization is needed you can use this method to set
  the name of the model this controller acts on.
- **setResultsPerPAge**(`20`): Sets the amount of results to show per page, defaults to 20.
- **setBreadcrumbs**(`Breadcrumbs $breadcrumbs`): Breadcrumbs to display.
- **eagerLoadListingRelations**(`['comments', 'author']`): Relations to eager load for the index view.
- **eagerLoadFormRelations**(`['comments', 'author']`): Relations to eager load for the form view.
- **eagerLoadFormRelationCounts**(`['comments', 'author']`): Relation count to eager load for the form view.

## Controller methods

There are a few methods that can be usefull to implement based on the needs of your application.


```php
    /*
     * Add anything you would like to have available in your module's index view (create modal)
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
```

You can also override all actions and internal functions, checkout the ModuleController source
in `A17\Twill\Http\Controllers\Admin\ModuleController`.

#### Example: sorting by a relationship field

Let's say we have a controller with certain fields displayed:

File: `app/Http/Controllers/Twill/PlayController.php`

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

### Additional table actions

You can override the `additionalTableActions()` method to add custom actions in your module's listing view:

File: `app/Http/Controllers/Twill/NewsletterController.php`

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

### Localizing the permalink

In a multilingual setup it might be interesting to define a localized permalink base.

We saw before that we can customize the permalink using `$permalinkBase` but if we want to localize this we can use the controller method `getLocalizedPermalinkBase`.

```php
protected function getLocalizedPermalinkBase()
{
    return [
        'en' => 'page',
        'nl' => 'pagina',
    ];
}
```

If you need more control or want to change the full permalink you can use the `formData` method instead.

The example below is a simple one and could be done as well with [customizing the permalink](#content-customizing-the-permalink)

```php
protected function formData($request)
{
    return [
        'localizedCustomPermalink' => [
            'en' => route('page', ['id' => $request->route('page')]),
            'nl' => route('page', ['id' => $request->route('page')])
        ]
    ];
}
```

### Customizing the permalink

If needed you can customize the permalink displayed in the admin interface when editing a model. This is especially useful if you are using Laravel for displaying your front-end as you do not need to keep your permalink and routes in sync.

![screenshot](/assets/custom-permalink.png)

This can be done by setting the `customPermalink` via the `formData` method in the model controller.

The example below will result in: `/page-route/3` for page with id 3.

```php
# Route definition
Route::get('page-route/{id}', function() {...})->name('page.detail');

# Method implementation
protected function formData($request)
{
    if ($request->route('page')) {
        return [
            'customPermalink' => route('page.detail', ['id' => $request->route('page')]),
        ];
    }
    return [];
}
```

