---
pageClass: twill-doc
---

# Controllers

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
