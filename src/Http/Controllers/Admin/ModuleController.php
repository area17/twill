<?php

namespace A17\Twill\Http\Controllers\Admin;

use A17\Twill\Exceptions\NoCapsuleFoundException;
use A17\Twill\Facades\TwillBlocks;
use A17\Twill\Facades\TwillCapsules;
use A17\Twill\Facades\TwillPermissions;
use A17\Twill\Helpers\FlashLevel;
use A17\Twill\Models\Behaviors\HasSlug;
use A17\Twill\Models\Contracts\TwillModelContract;
use A17\Twill\Models\Contracts\TwillSchedulableModel;
use A17\Twill\Repositories\ModuleRepository;
use A17\Twill\Services\Breadcrumbs\Breadcrumbs;
use A17\Twill\Services\Forms\Fields\BaseFormField;
use A17\Twill\Services\Forms\Fields\BlockEditor;
use A17\Twill\Services\Forms\Fields\Repeater;
use A17\Twill\Services\Forms\Form;
use A17\Twill\Services\Listings\Columns\Browser;
use A17\Twill\Services\Listings\Columns\FeaturedStatus;
use A17\Twill\Services\Listings\Columns\Image;
use A17\Twill\Services\Listings\Columns\Languages;
use A17\Twill\Services\Listings\Columns\NestedData;
use A17\Twill\Services\Listings\Columns\Presenter;
use A17\Twill\Services\Listings\Columns\PublishStatus;
use A17\Twill\Services\Listings\Columns\Relation;
use A17\Twill\Services\Listings\Columns\ScheduledStatus;
use A17\Twill\Services\Listings\Columns\Text;
use A17\Twill\Services\Listings\Filters\BasicFilter;
use A17\Twill\Services\Listings\Filters\FreeTextSearch;
use A17\Twill\Services\Listings\Filters\QuickFilter;
use A17\Twill\Services\Listings\Filters\QuickFilters;
use A17\Twill\Services\Listings\Filters\TableFilters;
use A17\Twill\Services\Listings\Filters\TwillBaseFilter;
use A17\Twill\Services\Listings\TableColumn;
use A17\Twill\Services\Listings\TableColumns;
use A17\Twill\Services\Listings\TableDataContext;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\View as IlluminateView;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

abstract class ModuleController extends Controller
{
    use Concerns\FormSubmitOptions;

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var string
     */
    protected $namespace;

    /**
     * @var string
     */
    protected $routePrefix;

    /**
     * @var string
     */
    protected $moduleName;

    /**
     * @var string
     */
    protected $modelName;

    /**
     * @var string
     */
    protected $modelTitle;

    protected ModuleRepository $repository;

    /**
     * @var \A17\Twill\Models\User
     */
    protected $user;

    /**
     * Options of the index view.
     *
     * @var array
     */
    protected $defaultIndexOptions = [
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
        'includeScheduledInList' => true,
        'showImage' => false,
        'sortable' => true,
    ];

    /**
     * Options of the index view and the corresponding auth gates.
     *
     * @var array
     */
    protected $authorizableOptions = [
        'list' => 'access-module-list',
        'create' => 'edit-module',
        'edit' => 'edit-item',
        'permalink' => 'edit-item',
        'publish' => 'edit-item',
        'feature' => 'edit-item',
        'reorder' => 'edit-module',
        'delete' => 'edit-item',
        'duplicate' => 'edit-item',
        'restore' => 'edit-item',
        'forceDelete' => 'edit-item',
        'bulkForceDelete' => 'edit-module',
        'bulkPublish' => 'edit-module',
        'bulkRestore' => 'edit-module',
        'bulkFeature' => 'edit-module',
        'bulkDelete' => 'edit-module',
        'bulkEdit' => 'edit-module',
        'editInModal' => 'edit-module',
        'skipCreateModal' => 'edit-module',
        'includeScheduledInList' => 'edit-module',
        'showImage' => 'edit-module',
        'sortable' => 'edit-module',
    ];

    /**
     * Relations to eager load for the index view.
     *
     * @var array
     */
    protected $indexWith = [];

    /**
     * Relations to eager load for the form view.
     *
     * @var array
     */
    protected $formWith = [];

    /**
     * Relation count to eager load for the form view.
     *
     * @var array
     */
    protected $formWithCount = [];

    /**
     * Additional filters for the index view.
     *
     * To automatically have your filter added to the index view use the following convention:
     * suffix the key containing the list of items to show in the filter by 'List' and
     * name it the same as the filter you defined in this array.
     *
     * Example: 'fCategory' => 'category_id' here and 'fCategoryList' in indexData()
     * By default, this will run a where query on the category_id column with the value
     * of fCategory if found in current request parameters. You can intercept this behavior
     * from your repository in the filter() function.
     *
     * @var array
     *
     * @deprecated use the method `filters` instead.
     */
    protected $filters = [];

    /**
     * Additional links to display in the listing filter.
     *
     * @var array
     */
    protected $filterLinks = [];

    /**
     * Default orders for the index view for fields that are not part of the indexColumns.
     *
     * @var array
     *
     * @deprecated when possible use getIndexTableColumns instead.
     */
    protected $defaultOrders = [
        'created_at' => 'desc',
    ];

    /**
     * @var int
     */
    protected $perPage = 20;

    /**
     * Name of the index column to use as name column.
     *
     * @var string
     */
    protected $titleColumnKey = 'title';

    /**
     * Label of the index column to use as name column.
     *
     * @var string
     */
    protected $titleColumnLabel = 'Title';

    /**
     * Name of the index column to use as identifier column.
     *
     * @var string
     */
    protected $identifierColumnKey = 'id';

    /**
     * Attribute to use as title in forms.
     *
     * @var string
     */
    protected $titleFormKey;

    /**
     * Label of the title field in forms.
     *
     * @var string
     */
    protected $titleFormLabel = 'Title';

    /**
     * Feature field name if the controller is using the feature route (defaults to "featured").
     *
     * @var string
     */
    protected $featureField = 'featured';

    /**
     * Indicates if this module is edited through a parent module.
     *
     * @var bool
     */
    protected $submodule = false;

    /**
     * @var int|null
     */
    protected $submoduleParentId = null;

    /**
     * Can be used in child classes to disable the content editor (full screen block editor).
     *
     * @var bool
     */
    protected $disableEditor = false;

    /**
     * @var array
     */
    protected $indexOptions;

    /**
     * @var array
     * @deprecated please use the getIndexTableColumns method. Will be removed in Twill 4.0
     */
    protected $indexColumns = [];

    /**
     * @var array
     * @deprecated please use the getBrowserTableColumns method. Will be removed in Twill 4.0
     */
    protected $browserColumns = [];

    /**
     * @var string
     */
    protected $permalinkBase;

    /**
     * Filters that are selected by default in the index view.
     *
     * Example: 'filter_key' => 'default_filter_value'
     *
     * @var array
     *
     * @deprecated use the method `default` in `filters` instead.
     */
    protected $filtersDefaultOptions = [];

    /**
     * @var array
     *
     * Can be something like ['search' => 'title|search']
     *
     * @deprecated use the method `default` in `filters` instead.
     */
    protected $defaultFilters;

    /**
     * @var string
     */
    protected $viewPrefix;

    /**
     * @var string
     */
    protected $previewView;

    /**
     * List of permissions keyed by a request field. Can be used to prevent unauthorized field updates.
     *
     * @var array
     */
    protected $fieldsPermissions = [];

    /**
     * Determines if draft revisions can be added on top of published content.
     *
     * @var bool
     */
    protected $enableDraftRevisions = false;

    /**
     * Array of customizable label translation keys.
     *
     * @var array
     */
    protected $labels = [];

    /**
     * When set to true and the model is translatable, the language prefix will not be shown in the permalink.
     */
    private bool $withoutLanguageInPermalink = false;

    /**
     * The columns to search for when using the search field.
     *
     * Do not modify this directly but use the method setSearchColumns().
     */
    protected ?array $searchColumns = null;

    /**
     * Default label translation keys that can be overridden in the labels array.
     *
     * @var array
     */
    protected $defaultLabels = [
        'published' => 'twill::lang.main.published',
        'draft' => 'twill::lang.main.draft',
        'listing' => [
            'filter' => [
                'published' => 'twill::lang.listing.filter.published',
                'draft' => 'twill::lang.listing.filter.draft',
            ],
        ],
    ];

    private ?Breadcrumbs $breadcrumbs = null;

    public function __construct(Application $app, Request $request)
    {
        parent::__construct();
        $this->app = $app;
        $this->request = $request;

        $this->setUpController();

        $this->modelName = $this->modelName ?? $this->getModelName();
        $this->routePrefix = $this->routePrefix ?? $this->getRoutePrefix();
        $this->namespace = $this->namespace ?? $this->getNamespace();
        $this->repository = $this->repository ?? $this->getRepository();
        $this->viewPrefix = $this->viewPrefix ?? $this->getViewPrefix();
        $this->modelTitle = $this->modelTitle ?? $this->getModelTitle();
        $this->labels = array_merge($this->defaultLabels, $this->labels);
        $this->middleware(function ($request, $next) {
            $this->user = auth('twill_users')->user();

            return $next($request);
        });

        if (!$this instanceof AppSettingsController) {
            $this->getForm($this->repository->getBaseModel())->registerDynamicRepeaters();
            $this->getSideFieldsets($this->repository->getBaseModel())->registerDynamicRepeaters();
        }

        // When no searchColumns are set we default to the title column key.
        if ($this->searchColumns === null) {
            $this->searchColumns = [$this->titleColumnKey];
        }
    }

    /**
     * The setup method that is called when the controller is booted.
     */
    protected function setUpController(): void
    {
    }

    /**
     * Removes the "Create" button on the listing page.
     */
    protected function disableCreate(): void
    {
        $this->indexOptions['create'] = false;
    }

    /**
     * Disables table interaction and removes edit links.
     */
    protected function disableEdit(): void
    {
        $this->indexOptions['edit'] = false;
    }

    /**
     * Disables the ability to sort the table by clicking table headers.
     */
    protected function disableSortable(): void
    {
        $this->indexOptions['sortable'] = false;
    }

    /**
     * Removes the publish/un-publish icon on the content listing.
     */
    protected function disablePublish(): void
    {
        $this->indexOptions['publish'] = false;
    }

    /**
     * Removes the "publish" option from the bulk operations.
     */
    protected function disableBulkPublish(): void
    {
        $this->indexOptions['bulkPublish'] = false;
    }

    /**
     * Removes "restore" from the list item dropdown on the "Trash" content list.
     */
    protected function disableRestore(): void
    {
        $this->indexOptions['restore'] = false;
    }

    /**
     * Removes the "Trash" quick filter.
     */
    protected function disableBulkRestore(): void
    {
        $this->indexOptions['bulkRestore'] = false;
    }

    /**
     * Removes the "delete" option from the "Trash" content list.
     */
    protected function disableForceDelete(): void
    {
        $this->indexOptions['forceDelete'] = false;
    }

    /**
     * Removes "restore" from the bulk operations on the "Trash" content list.
     */
    protected function disableBulkForceDelete(): void
    {
        $this->indexOptions['bulkForceDelete'] = false;
    }

    /**
     * Removes the "delete" option from the content lists.
     */
    protected function disableDelete(): void
    {
        $this->indexOptions['delete'] = false;
    }

    /**
     * Removes the "delete" option from the bulk operations.
     */
    protected function disableBulkDelete(): void
    {
        $this->indexOptions['bulkDelete'] = false;
    }

    /**
     * Removes the permalink from the create/edit screens.
     */
    protected function disablePermalink(): void
    {
        $this->indexOptions['permalink'] = false;
    }

    /**
     * Disables the editor button.
     */
    protected function disableEditor(): void
    {
        $this->disableEditor = true;
    }

    /**
     * Disables bulk operations.
     */
    protected function disableBulkEdit(): void
    {
        $this->indexOptions['bulkEdit'] = false;
    }

    /**
     * Hides publish scheduling information from the content list.
     *
     * This does not affect custom table builders. Unless implemented.
     */
    protected function disableIncludeScheduledInList(): void
    {
        $this->indexOptions['includeScheduledInList'] = false;
    }

    /**
     * Disables the create modal and directly forwards you to the full edit page.
     */
    protected function enableSkipCreateModal(): void
    {
        $this->indexOptions['skipCreateModal'] = true;
    }

    /**
     * Allow to feature the content. This requires a 'featured' fillable boolean on the model.
     *
     * If you want to use a different column you can use the `setFeaturedField` method.
     */
    protected function enableFeature(): void
    {
        // @todo: Also expand on the documentation about this.
        // Also mention isUniqueFeature that only one can be featured + test this.
        $this->indexOptions['feature'] = true;
    }

    /**
     * Enables the "Feature" bulk operation.
     */
    protected function enableBulkFeature(): void
    {
        $this->indexOptions['bulkFeature'] = true;
    }

    /**
     * Enables the "Duplicate" option from the content lists.
     */
    protected function enableDuplicate(): void
    {
        $this->indexOptions['duplicate'] = true;
    }

    /**
     * Allows to reorder the items, if this was setup on the model.
     */
    protected function enableReorder(): void
    {
        $this->indexOptions['reorder'] = true;
    }

    /**
     * Enables the function that content is edited in the create modal.
     */
    protected function enableEditInModal(): void
    {
        // @3xtodo: When this is enabled, the "link" to the model in the listing does not work (Redirects back).
        $this->indexOptions['editInModal'] = true;
    }

    /**
     * Shows the thumbnail of the content in the list.
     */
    protected function enableShowImage(): void
    {
        $this->indexOptions['showImage'] = true;
    }

    /**
     * Set the field to use for featuring content.
     */
    protected function setFeatureField(string $field): void
    {
        $this->featureField = $field;
    }

    /**
     * Set the columns to search in.
     *
     * SearchColumns are automatically prefixes/suffixed with %.
     */
    protected function setSearchColumns(array $searchColumns): void
    {
        $this->searchColumns = $searchColumns;
    }

    /**
     * Set the name of the module you are working with.
     */
    protected function setModuleName(string $moduleName): void
    {
        $this->moduleName = $moduleName;
    }

    /**
     * The static permalink base to your module. Defaults to `setModuleName` when empty.
     */
    protected function setPermalinkBase(string $permalinkBase): void
    {
        $this->permalinkBase = $permalinkBase;
    }

    protected function withoutLanguageInPermalink(bool $without = true): void
    {
        $this->withoutLanguageInPermalink = $without;
    }

    /**
     * Sets the field to use as title, defaults to `title`.
     */
    protected function setTitleColumnKey(string $titleColumnKey): void
    {
        $this->titleColumnKey = $titleColumnKey;
    }

    /**
     * Sets the label to use for title column, defaults to `Title`.
     */
    protected function setTitleColumnLabel(string $titleColumnLabel): void
    {
        $this->titleColumnLabel = $titleColumnLabel;
    }

    /**
     * Sets the field to use as title in forms, defaults to `title`.
     */
    protected function setTitleFormKey(string $titleFormKey): void
    {
        $this->titleFormKey = $titleFormKey;
    }

    /**
     * Sets the label to use for title field in forms, defaults to `Title`.
     */
    protected function setTitleFormLabel(string $titleFormLabel): void
    {
        $this->titleFormLabel = $titleFormLabel;
    }

    /**
     * Usually not required, but in case customization is needed you can use this method to set the name of the model
     * this controller acts on.
     */
    protected function setModelName(string $modelName): void
    {
        $this->modelName = $modelName;
    }

    /**
     * Sets the amount of results to show per page, defaults to 20.
     */
    protected function setResultsPerPage(int $resultsPerPage): void
    {
        $this->perPage = $resultsPerPage;
    }

    /**
     * Relations to eager load for the index view.
     */
    protected function eagerLoadListingRelations(array $relations): void
    {
        $this->indexWith = $relations;
    }

    /**
     * Relations to eager load for the form view.
     *
     * Add relationship used in multiselect and resource form fields.
     */
    protected function eagerLoadFormRelations(array $relations): void
    {
        $this->formWith = $relations;
    }

    /**
     * Relation count to eager load for the form view.
     */
    protected function eagerLoadFormRelationCounts(array $relations): void
    {
        $this->formWithCount = $relations;
    }

    /**
     * Set the breadcrumbs.
     */
    protected function setBreadcrumbs(Breadcrumbs $breadcrumbs): void
    {
        $this->breadcrumbs = $breadcrumbs;
    }

    /**
     * $type can be index or browser.
     */
    private function getTableColumns(string $type): TableColumns
    {
        if ($type === 'index') {
            $tableColumns = $this->getIndexTableColumns();
        } else {
            $tableColumns = $this->getBrowserTableColumns();
        }

        return $tableColumns->each(function (TableColumn $column) {
            if ($column instanceof NestedData) {
                $column->linkCell(function (TwillModelContract $model, NestedData $column) {
                    $module = Str::singular(last(explode('.', $this->moduleName)));

                    return moduleRoute(
                        "$this->moduleName." . $column->getField(),
                        $this->routePrefix,
                        'index',
                        [$module => $this->getItemIdentifier($model)]
                    );
                });
            } elseif ($column->shouldLinkToEdit()) {
                $column->linkCell(function (TwillModelContract $model) {
                    if ($model->trashed()) {
                        return null;
                    }

                    if ($this->getIndexOption('edit', $model)) {
                        return $this->getModuleRoute($model->id, 'edit');
                    }
                });
            }
        });
    }

    protected function getBrowserTableColumns(): TableColumns
    {
        $columns = TableColumns::make();

        if ($this->browserColumns !== []) {
            $this->handleLegacyColumns($columns, $this->browserColumns);
        } elseif ($this->moduleHas('medias')) {
            $columns->add(
                Image::make()
                    ->field('thumbnail')
                    ->rounded()
                    ->title(twillTrans('Image'))
            );
        }

        $columns = $columns->merge($this->additionalBrowserTableColumns());

        return $columns;
    }

    protected function getIndexTableColumns(): TableColumns
    {
        $columns = TableColumns::make();

        if ($this->getIndexOption('publish')) {
            $columns->add(
                PublishStatus::make()
                    ->title(twillTrans('twill::lang.listing.columns.published'))
                    ->sortable()
                    ->optional()
            );
        }

        if ($this->indexColumns === []) {
            // Add default columns.
            if ($this->getIndexOption('showImage')) {
                $columns->add(
                    Image::make()
                        ->field('thumbnail')
                        ->title(twillTrans('Image'))
                );
            }

            if ($this->getIndexOption('feature') && $this->repository->isFillable('featured')) {
                $columns->add(
                    FeaturedStatus::make()
                        ->title(twillTrans('twill::lang.listing.columns.featured'))
                );
            }
        }

        // Consume Deprecated data.
        if ($this->indexColumns !== []) {
            $this->handleLegacyColumns($columns, $this->indexColumns);
        } else {
            $columns->add(
                Text::make()
                    ->field($this->titleColumnKey)
                    ->title($this->titleColumnKey === 'title' && $this->titleColumnLabel === 'Title' ? twillTrans('twill::lang.main.title') : $this->titleColumnLabel)
                    ->sortable()
                    ->linkToEdit()
            );
        }

        $columns = $columns->merge($this->additionalIndexTableColumns());

        if ($this->getIndexOption('includeScheduledInList') && $this->repository->isFillable('publish_start_date')) {
            $columns->add(
                ScheduledStatus::make()
                    ->title(twillTrans('twill::lang.publisher.scheduled'))
                    ->optional()
            );
        }

        if ($this->moduleHas('translations') && count(getLocales()) > 1) {
            $columns->add(
                Languages::make()
                    ->title(twillTrans('twill::lang.listing.languages'))
                    ->optional()
            );
        }

        return $columns;
    }

    /**
     * Similar to @see getBrowserTableColumns but these will be added on top of the default columns.
     */
    protected function additionalBrowserTableColumns(): TableColumns
    {
        return new TableColumns();
    }

    /**
     * Similar to @see getIndexTableColumns but these will be added on top of the default columns.
     */
    protected function additionalIndexTableColumns(): TableColumns
    {
        return new TableColumns();
    }

    private function handleLegacyColumns(TableColumns $columns, array $items): void
    {
        foreach ($items as $key => $indexColumn) {
            if ($indexColumn['nested'] ?? false) {
                $columns->add(
                    NestedData::make()
                        ->title($indexColumn['title'] ?? null)
                        ->field($indexColumn['nested'])
                        ->sortKey($indexColumn['sortKey'] ?? null)
                        ->sortable($indexColumn['sort'] ?? false)
                        ->optional($indexColumn['optional'] ?? false)
                        ->linkCell(function (TwillModelContract $model) use ($indexColumn) {
                            $module = Str::singular(last(explode('.', $this->moduleName)));

                            return moduleRoute(
                                "$this->moduleName.{$indexColumn['nested']}",
                                $this->routePrefix,
                                'index',
                                [$module => $this->getItemIdentifier($model)]
                            );
                        })
                );
            } elseif ($indexColumn['thumb'] ?? false) {
                $columns->add(
                    Image::make()
                        ->title($indexColumn['title'] ?? $key)
                        ->role($indexColumn['variant']['role'] ?? null)
                        ->crop($indexColumn['variant']['crop'] ?? null)
                        ->field($indexColumn['field'] ?? $key)
                        ->sortKey($indexColumn['sortKey'] ?? null)
                        ->optional($indexColumn['optional'] ?? false)
                );
            } elseif ($indexColumn['relatedBrowser'] ?? false) {
                $columns->add(
                    Browser::make()
                        ->title($indexColumn['title'])
                        ->field($indexColumn['field'] ?? $key)
                        ->sortKey($indexColumn['sortKey'] ?? null)
                        ->optional($indexColumn['optional'] ?? false)
                        ->browser($indexColumn['relatedBrowser'])
                );
            } elseif ($indexColumn['relationship'] ?? false) {
                $columns->add(
                    Relation::make()
                        ->title($indexColumn['title'])
                        ->field($indexColumn['field'] ?? $key)
                        ->sortKey($indexColumn['sortKey'] ?? null)
                        ->optional($indexColumn['optional'] ?? false)
                        ->relation($indexColumn['relationship'])
                );
            } elseif ($indexColumn['present'] ?? false) {
                $columns->add(
                    Presenter::make()
                        ->title($indexColumn['title'])
                        ->field($indexColumn['field'] ?? $key)
                        ->sortKey($indexColumn['sortKey'] ?? null)
                        ->optional($indexColumn['optional'] ?? false)
                        ->sortable($indexColumn['sort'] ?? false)
                );
            } else {
                $textColumn = Text::make()
                    ->title($indexColumn['title'] ?? null)
                    ->field($indexColumn['field'] ?? $key)
                    ->sortKey($indexColumn['sortKey'] ?? null)
                    ->optional($indexColumn['optional'] ?? false)
                    ->sortable($indexColumn['sort'] ?? false);

                // If it is a the title, we always want to link it.
                if ($this->titleColumnKey === ($indexColumn['field'] ?? $key)) {
                    $textColumn->linkCell(function (TwillModelContract $model) {
                        if ($this->getIndexOption('edit', $model)) {
                            return $this->getModuleRoute($model->id, 'edit');
                        }
                    });
                }
                $columns->add($textColumn);
            }
        }
    }

    /**
     * Match an option name to a gate name if needed, then authorize it.
     *
     * @return void
     */
    protected function authorizeOption($option, $arguments = [])
    {
        $gate = $this->authorizableOptions[$option] ?? $option;

        $this->authorize($gate, $arguments);
    }

    /**
     * @return void
     * @deprecated To be removed in Twill 3.0
     * @todo: Check this.
     */
    protected function setMiddlewarePermission()
    {
        $this->middleware('can:list', ['only' => ['index', 'show']]);
        $this->middleware('can:edit', ['only' => ['store', 'edit', 'update']]);
        $this->middleware('can:duplicate', ['only' => ['duplicate']]);
        $this->middleware('can:publish', ['only' => ['publish', 'feature', 'bulkPublish', 'bulkFeature']]);
        $this->middleware('can:reorder', ['only' => ['reorder']]);
        $this->middleware(
            'can:delete',
            [
                'only' => [
                    'destroy',
                    'bulkDelete',
                    'restore',
                    'bulkRestore',
                    'forceDelete',
                    'bulkForceDelete',
                    'restoreRevision',
                ],
            ]
        );
    }

    /**
     * @param Request $request
     * @return string|int|null
     */
    protected function getParentModuleIdFromRequest(Request $request)
    {
        $moduleParts = explode('.', $this->moduleName);

        if (count($moduleParts) > 1) {
            $parentModule = Str::singular($moduleParts[count($moduleParts) - 2]);

            return $request->route()->parameters()[$parentModule];
        }

        return null;
    }

    /**
     * @return IlluminateView|JsonResponse
     */
    public function index(?int $parentModuleId = null): mixed
    {
        $this->authorizeOption('list', $this->moduleName);

        $parentModuleId = $this->getParentModuleIdFromRequest($this->request) ?? $parentModuleId;

        $this->submodule = isset($parentModuleId);
        $this->submoduleParentId = $parentModuleId;

        $indexData = $this->getIndexData(
            $this->submodule ? [
                $this->getParentModuleForeignKey() => $this->submoduleParentId,
            ] : []
        );

        if ($this->request->ajax() || $this->request->expectsJson()) {
            return new JsonResponse($indexData + ['replaceUrl' => true]);
        }

        if ($this->request->has('openCreate') && $this->request->get('openCreate')) {
            $indexData += ['openCreate' => true];
        }

        $form = $this->getCreateForm();

        if (
            $form->filter(function (BaseFormField $field) {
                return $field instanceof BlockEditor ||
                    $field instanceof Repeater;
            })
                ->isNotEmpty()
        ) {
            throw new \Exception('Create forms do not support repeaters and blocks');
        }

        if ($form->isNotEmpty()) {
            $view = 'twill::layouts.listing';
        } else {
            $view = Collection::make([
                "$this->viewPrefix.index",
                "twill::$this->moduleName.index",
                'twill::layouts.listing',
            ])->first(function ($view) {
                return View::exists($view);
            });
        }

        return View::make($view, $indexData + ['repository' => $this->repository])
            ->with(['formBuilder' => $form->toFrontend(isCreate: true)]);
    }

    public function getCreateForm(): Form
    {
        return new Form();
    }

    public function browser(): JsonResponse
    {
        return Response::json($this->getBrowserData());
    }

    /**
     * @param int|null $parentModuleId
     * @return \Illuminate\Http\JsonResponse
     */
    public function store($parentModuleId = null)
    {
        $this->authorizeOption('create', $this->moduleName);

        $parentModuleId = $this->getParentModuleIdFromRequest($this->request) ?? $parentModuleId;

        $input = $this->validateFormRequest()->all();
        $optionalParent = $parentModuleId ? [$this->getParentModuleForeignKey() => $parentModuleId] : [];

        if (isset($input['cmsSaveType']) && $input['cmsSaveType'] === 'cancel') {
            return $this->respondWithRedirect(
                moduleRoute(
                    $this->moduleName,
                    $this->routePrefix,
                    'create'
                )
            );
        }

        $item = $this->repository->create($input + $optionalParent);

        activity()->performedOn($item)->log('created');

        $this->fireEvent($input);

        Session::put($this->moduleName . '_retain', true);

        if ($this->getIndexOption('editInModal')) {
            return $this->respondWithSuccess(twillTrans('twill::lang.publisher.save-success'));
        }

        if (isset($input['cmsSaveType']) && Str::endsWith($input['cmsSaveType'], '-close')) {
            return $this->respondWithRedirect($this->getBackLink());
        }

        if (isset($input['cmsSaveType']) && Str::endsWith($input['cmsSaveType'], '-new')) {
            return $this->respondWithRedirect(
                moduleRoute(
                    $this->moduleName,
                    $this->routePrefix,
                    'create'
                )
            );
        }

        return $this->respondWithRedirect(
            moduleRoute(
                $this->moduleName,
                $this->routePrefix,
                'edit',
                [Str::singular(last(explode('.', $this->moduleName))) => $this->getItemIdentifier($item)]
            )
        );
    }

    /**
     * @param Request $request
     * @param int|$id
     * @param int|null $submoduleId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function show($id, $submoduleId = null)
    {
        if ($this->getIndexOption('editInModal')) {
            return Redirect::to(moduleRoute($this->moduleName, $this->routePrefix, 'index'));
        }

        return $this->redirectToForm($this->getParentModuleIdFromRequest($this->request) ?? $submoduleId ?? $id);
    }

    /**
     * @return array{
     *          item: TwillModelContract,
     *          id: int
     *     }
     */
    private function itemAndIdFromRequest(TwillModelContract|int $id): array
    {
        if ($id instanceof TwillModelContract) {
            $item = $id;
            $id = $item->id;
        } else {
            $parameter = Str::singular(Str::afterLast($this->moduleName, '.'));
            $id = (int) $this->request->route()->parameter($parameter, $id);
            $item = $this->repository->getById($id, $this->formWith, $this->formWithCount);
        }

        return [
            $item,
            $id,
        ];
    }

    public function edit(TwillModelContract|int $id): mixed
    {
        [$item, $id] = $this->itemAndIdFromRequest($id);

        $this->authorizeOption('edit', $item);

        if ($this->getIndexOption('editInModal')) {
            return $this->request->ajax()
                ? Response::json($this->modalFormData($item))
                : Redirect::to(moduleRoute($this->moduleName, $this->routePrefix, 'index'));
        }

        $this->setBackLink();

        $controllerForm = $this->getForm($item);

        if ($controllerForm->hasForm()) {
            $view = 'twill::layouts.form';
        } else {
            $view = Collection::make([
                "$this->viewPrefix.form",
                "twill::$this->moduleName.form",
                'twill::layouts.form',
            ])->first(function ($view) {
                return View::exists($view);
            });
        }

        if ($this->moduleHas('revisions')) {
            $latestRevision = $item->revisions->first();

            if ($latestRevision && $latestRevision->isDraft()) {
                Session::flash('status', twillTrans('twill::lang.publisher.draft-revisions-available'));
            }
        }

        return View::make($view, $this->form($id))->with(
            ['formBuilder' => $controllerForm->toFrontend($this->getSideFieldsets($item))]
        );
    }

    public function create(int $parentModuleId = null): JsonResponse|RedirectResponse|IlluminateView
    {
        if (! $this->getIndexOption('skipCreateModal')) {
            return Redirect::to(
                moduleRoute(
                    $this->moduleName,
                    $this->routePrefix,
                    'index',
                    ['openCreate' => true]
                )
            );
        }

        $parentModuleId = $this->getParentModuleIdFromRequest($this->request) ?? $parentModuleId;

        $this->submodule = isset($parentModuleId);
        $this->submoduleParentId = $parentModuleId;

        $emptyModelInstance = $this->repository->newInstance();
        $controllerForm = $this->getForm($emptyModelInstance);

        $view = Collection::make([
            "$this->viewPrefix.form",
            "twill::$this->moduleName.form",
            'twill::layouts.form',
        ])->first(function ($view) {
            return View::exists($view);
        });

        View::share('form', $this->form(null));

        return View::make($view, $this->form(null))->with(
            ['formBuilder' => $controllerForm->toFrontend($this->getSideFieldsets($emptyModelInstance), true)]
        );
    }

    public function update(int|TwillModelContract $id, ?int $submoduleId = null): JsonResponse
    {
        [$item, $id] = $this->itemAndIdFromRequest($id);

        $this->authorizeOption('edit', $item);

        $input = $this->request->all();

        if (isset($input['cmsSaveType']) && $input['cmsSaveType'] === 'cancel') {
            return $this->respondWithRedirect(
                moduleRoute(
                    $this->moduleName,
                    $this->routePrefix,
                    'edit',
                    [Str::singular($this->moduleName) => $id]
                )
            );
        }

        $this->performUpdate($item);

        if (isset($input['cmsSaveType'])) {
            if (Str::endsWith($input['cmsSaveType'], '-close')) {
                return $this->respondWithRedirect($this->getBackLink());
            }

            if (Str::endsWith($input['cmsSaveType'], '-new')) {
                if ($this->getIndexOption('skipCreateModal')) {
                    return $this->respondWithRedirect(
                        moduleRoute(
                            $this->moduleName,
                            $this->routePrefix,
                            'create'
                        )
                    );
                }

                return $this->respondWithRedirect(
                    moduleRoute(
                        $this->moduleName,
                        $this->routePrefix,
                        'index',
                        ['openCreate' => true]
                    )
                );
            }

            if ($input['cmsSaveType'] === 'restore') {
                Session::flash('status', twillTrans('twill::lang.publisher.restore-success'));

                return $this->respondWithRedirect(
                    moduleRoute(
                        $this->moduleName,
                        $this->routePrefix,
                        'edit',
                        [Str::singular($this->moduleName) => $id]
                    )
                );
            }
        }

        if ($this->moduleHas('revisions')) {
            return Response::json([
                'message' => twillTrans('twill::lang.publisher.save-success'),
                'variant' => FlashLevel::SUCCESS,
                'revisions' => $item->refresh()->revisionsArray(),
            ]);
        }

        return $this->respondWithSuccess(twillTrans('twill::lang.publisher.save-success'));
    }

    protected function performUpdate($item): void
    {
        $formRequest = $this->validateFormRequest();
        $data = $formRequest->all();

        if (Str::startsWith($data['cmsSaveType'] ?? '', 'draft-revision')) {
            $data['published'] = false;

            $this->repository->createRevisionIfNeeded($item, $data);
        } else {
            $this->repository->update($item->id, $data);

            activity()->performedOn($item)->log('updated');

            $this->fireEvent();
        }
    }

    public function preview(int $id): IlluminateView
    {
        if ($this->request->has('revisionId')) {
            $item = $this->repository->previewForRevision($id, $this->request->get('revisionId'));
        } else {
            $formRequest = $this->validateFormRequest();
            $item = $this->repository->preview($id, $formRequest->all());
        }

        if ($this->request->has('activeLanguage')) {
            App::setLocale($this->request->get('activeLanguage'));
        }

        $previewView = $this->previewView ?? (Config::get('twill.frontend.views_path', 'site') . '.' . Str::singular(
            $this->moduleName
        ));

        return View::exists($previewView) ? View::make(
            $previewView,
            array_replace([
                'item' => $item,
            ], $this->previewData($item))
        ) : View::make('twill::errors.preview', [
            'moduleName' => Str::singular($this->moduleName),
        ]);
    }

    /**
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function restoreRevision($id)
    {
        if ($this->request->has('revisionId')) {
            $item = $this->repository->previewForRevision($id, $this->request->get('revisionId'));
            $item[$this->identifierColumnKey] = $id;
            $item->cmsRestoring = true;
        } else {
            throw new NotFoundHttpException();
        }

        $this->setBackLink();

        $controllerForm = $this->getForm($item);

        $view = Collection::make([
            "$this->viewPrefix.form",
            "twill::$this->moduleName.form",
            'twill::layouts.form',
        ])->first(function ($view) {
            return View::exists($view);
        });

        $revision = $item->revisions()->where('id', $this->request->get('revisionId'))->first();

        if ($revision->isDraft()) {
            Session::flash('restoreMessage', twillTrans('twill::lang.publisher.editing-draft-revision'));
        } else {
            $date = $revision->created_at->toDayDateTimeString();
            Session::flash(
                'restoreMessage',
                twillTrans('twill::lang.publisher.restore-message', ['user' => $revision->byUser, 'date' => $date])
            );
        }

        View::share('form', $this->form($id, $item));

        return View::make($view, $this->form($id, $item))->with(
            ['formBuilder' => $controllerForm->toFrontend($this->getSideFieldsets($item))]
        );
    }

    public function publish(): JsonResponse
    {
        try {
            $data = $this->validate($this->request, [
                'id' => 'integer|required',
                'active' => 'bool|required',
            ]);

            if (
                $this->repository->updateBasic($data['id'], [
                    'published' => ! $data['active'],
                ])
            ) {
                activity()->performedOn(
                    $this->repository->getById($data['id'])
                )->log(
                    ($this->request->get('active') ? 'un' : '') . 'published'
                );

                $this->fireEvent();

                if ($data['active']) {
                    return $this->respondWithSuccess(
                        twillTrans('twill::lang.listing.publish.unpublished', ['modelTitle' => $this->modelTitle])
                    );
                }

                return $this->respondWithSuccess(
                    twillTrans('twill::lang.listing.publish.published', ['modelTitle' => $this->modelTitle])
                );
            }
        } catch (\Exception $e) {
            Log::error($e);
        }

        return $this->respondWithError(
            twillTrans('twill::lang.listing.publish.error', ['modelTitle' => $this->modelTitle])
        );
    }

    public function bulkPublish(): JsonResponse
    {
        try {
            if (
                $this->repository->updateBasic(explode(',', $this->request->get('ids')), [
                    'published' => $this->request->get('publish'),
                ])
            ) {
                $this->fireEvent();
                if ($this->request->get('publish')) {
                    return $this->respondWithSuccess(
                        twillTrans('twill::lang.listing.bulk-publish.published', ['modelTitle' => $this->modelTitle])
                    );
                } else {
                    return $this->respondWithSuccess(
                        twillTrans('twill::lang.listing.bulk-publish.unpublished', ['modelTitle' => $this->modelTitle])
                    );
                }
            }
        } catch (\Exception $e) {
            Log::error($e);
        }

        return $this->respondWithError(
            twillTrans('twill::lang.listing.bulk-publish.error', ['modelTitle' => $this->modelTitle])
        );
    }

    public function duplicate(int|TwillModelContract $id, ?int $submoduleId = null): JsonResponse
    {
        [$item, $id] = $this->itemAndIdFromRequest($id);

        if ($newItem = $this->repository->duplicate($id, $this->titleColumnKey)) {
            $this->fireEvent();
            activity()->performedOn($item)->log('duplicated');

            // Handle nested module.
            if (Str::contains($this->moduleName, '.')) {
                $moduleName = Str::afterLast($this->moduleName, '.');
                $singularParentModuleName = Str::singular(Str::beforeLast($this->moduleName, '.'));

                $parameters = [
                    Str::singular($moduleName) => $newItem->id,
                    $singularParentModuleName => request()->query($singularParentModuleName),
                ];
            } else {
                $parameters = [
                    Str::singular($this->moduleName) => $newItem->id,
                ];
            }

            return Response::json([
                'message' => twillTrans('twill::lang.listing.duplicate.success', ['modelTitle' => $this->modelTitle]),
                'variant' => FlashLevel::SUCCESS,
                'redirect' => moduleRoute(
                    $this->moduleName,
                    $this->routePrefix,
                    'edit',
                    array_filter($parameters)
                ),
            ]);
        }

        return $this->respondWithError(
            twillTrans('twill::lang.listing.duplicate.error', ['modelTitle' => $this->modelTitle])
        );
    }

    public function destroy(int|TwillModelContract $id, ?int $submoduleId = null): JsonResponse
    {
        [$item, $id] = $this->itemAndIdFromRequest($id);

        if ($this->repository->delete($id)) {
            $this->fireEvent();
            activity()->performedOn($item)->log('deleted');

            return $this->respondWithSuccess(
                twillTrans('twill::lang.listing.delete.success', ['modelTitle' => $this->modelTitle])
            );
        }

        return $this->respondWithError(
            twillTrans('twill::lang.listing.delete.error', ['modelTitle' => $this->modelTitle])
        );
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkDelete()
    {
        if ($this->repository->bulkDelete(explode(',', $this->request->get('ids')))) {
            $this->fireEvent();

            return $this->respondWithSuccess(
                twillTrans('twill::lang.listing.bulk-delete.success', ['modelTitle' => $this->modelTitle])
            );
        }

        return $this->respondWithError(
            twillTrans('twill::lang.listing.bulk-delete.error', ['modelTitle' => $this->modelTitle])
        );
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function forceDelete()
    {
        if ($this->repository->forceDelete($this->request->get('id'))) {
            $this->fireEvent();

            return $this->respondWithSuccess(
                twillTrans('twill::lang.listing.force-delete.success', ['modelTitle' => $this->modelTitle])
            );
        }

        return $this->respondWithError(
            twillTrans('twill::lang.listing.force-delete.error', ['modelTitle' => $this->modelTitle])
        );
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkForceDelete()
    {
        if ($this->repository->bulkForceDelete(explode(',', $this->request->get('ids')))) {
            $this->fireEvent();

            return $this->respondWithSuccess(
                twillTrans('twill::lang.listing.bulk-force-delete.success', ['modelTitle' => $this->modelTitle])
            );
        }

        return $this->respondWithError(
            twillTrans('twill::lang.listing.bulk-force-delete.error', ['modelTitle' => $this->modelTitle])
        );
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore()
    {
        if ($this->repository->restore($this->request->get('id'))) {
            $this->fireEvent();
            activity()->performedOn($this->repository->getById($this->request->get('id')))->log('restored');

            return $this->respondWithSuccess(
                twillTrans('twill::lang.listing.restore.success', ['modelTitle' => $this->modelTitle])
            );
        }

        return $this->respondWithError(
            twillTrans('twill::lang.listing.restore.error', ['modelTitle' => $this->modelTitle])
        );
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkRestore()
    {
        if ($this->repository->bulkRestore(explode(',', $this->request->get('ids')))) {
            $this->fireEvent();

            return $this->respondWithSuccess(
                twillTrans('twill::lang.listing.bulk-restore.success', ['modelTitle' => $this->modelTitle])
            );
        }

        return $this->respondWithError(
            twillTrans('twill::lang.listing.bulk-restore.error', ['modelTitle' => $this->modelTitle])
        );
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function feature()
    {
        if (($id = $this->request->get('id'))) {
            $featuredField = $this->request->get('featureField') ?? $this->featureField;
            $featured = ! $this->request->get('active');

            if ($this->repository->isUniqueFeature()) {
                if ($featured) {
                    $this->repository->updateBasic(null, [$featuredField => false]);
                    $this->repository->updateBasic($id, [$featuredField => $featured]);
                }
            } else {
                $this->repository->updateBasic($id, [$featuredField => $featured]);
            }

            activity()->performedOn(
                $this->repository->getById($id)
            )->log(
                ($this->request->get('active') ? 'un' : '') . 'featured'
            );

            $this->fireEvent();

            if ($this->request->get('active')) {
                return $this->respondWithSuccess(
                    twillTrans('twill::lang.listing.featured.unfeatured', ['modelTitle' => $this->modelTitle])
                );
            } else {
                return $this->respondWithSuccess(
                    twillTrans('twill::lang.listing.featured.featured', ['modelTitle' => $this->modelTitle])
                );
            }
        }

        return $this->respondWithError(
            twillTrans('twill::lang.listing.featured.error', ['modelTitle' => $this->modelTitle])
        );
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkFeature()
    {
        if (($ids = explode(',', $this->request->get('ids')))) {
            $featuredField = $this->request->get('featureField') ?? $this->featureField;
            $featured = $this->request->get('feature') ?? true;
            // we don't need to check if unique feature since bulk operation shouldn't be allowed in this case
            $this->repository->updateBasic($ids, [$featuredField => $featured]);
            $this->fireEvent();

            if ($this->request->get('feature')) {
                return $this->respondWithSuccess(
                    twillTrans('twill::lang.listing.bulk-featured.featured', ['modelTitle' => $this->modelTitle])
                );
            } else {
                return $this->respondWithSuccess(
                    twillTrans('twill::lang.listing.bulk-featured.unfeatured', ['modelTitle' => $this->modelTitle])
                );
            }
        }

        return $this->respondWithError(
            twillTrans('twill::lang.listing.bulk-featured.error', ['modelTitle' => $this->modelTitle])
        );
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function reorder()
    {
        if ($values = $this->request->get('ids', null)) {
            $this->repository->setNewOrder($values);
            $this->fireEvent();

            return $this->respondWithSuccess(
                twillTrans('twill::lang.listing.reorder.success', ['modelTitle' => $this->modelTitle])
            );
        }

        return $this->respondWithError(
            twillTrans('twill::lang.listing.reorder.error', ['modelTitle' => $this->modelTitle])
        );
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function tags()
    {
        $query = $this->request->input('q');
        $tags = $this->repository->getTags($query);

        return Response::json([
            'items' => $tags->map(function ($tag) {
                return $tag->name;
            }),
        ], 200);
    }

    /**
     * @return array
     */
    public function additionalTableActions()
    {
        return [];
    }

    protected function getIndexData(array $prependScope = []): array
    {
        $items = $this->getIndexItems($prependScope);

        $data = [
                'tableData' => $this->getIndexTableData($items),
                'tableColumns' => $this->getTableColumns('index')->toCmsArray(
                    request(),
                    $this->getIndexOption('sortable')
                ),
                'tableMainFilters' => $this->quickFilters()->toFrontendArray(),
                'filters' => json_decode($this->request->get('filter'), true) ?? [],
                // HiddenFilters are called "hidden" because they only show when the filters button is clicked.
                'hiddenFilters' => $this->filters(),
                'filterLinks' => $this->filterLinks ?? [],
                'maxPage' => method_exists($items, 'lastPage') ? $items->lastPage() : 1,
                'defaultMaxPage' => method_exists($items, 'total') ? ceil($items->total() / $this->perPage) : 1,
                'offset' => method_exists($items, 'perPage') ? $items->perPage() : count($items),
                'defaultOffset' => $this->perPage,
            ] + $this->getIndexUrls($this->moduleName, $this->routePrefix);

        $baseUrl = $this->getPermalinkBaseUrl();

        $options = [
            'moduleName' => $this->moduleName,
            'skipCreateModal' => $this->getIndexOption('skipCreateModal'),
            'reorder' => $this->getIndexOption('reorder'),
            'create' => $this->getIndexOption('create'),
            'duplicate' => $this->getIndexOption('duplicate'),
            'translate' => $this->moduleHas('translations'),
            'translateTitle' => $this->titleIsTranslatable(),
            'permalink' => $this->getIndexOption('permalink'),
            'bulkEdit' => $this->getIndexOption('bulkEdit'),
            'titleFormKey' => $this->titleFormKey ?? $this->titleColumnKey,
            'titleFormLabel' => $this->titleFormLabel ?? $this->titleColumnLabel,
            'baseUrl' => $baseUrl,
            'permalinkPrefix' => $this->getPermalinkPrefix($baseUrl),
            'additionalTableActions' => $this->additionalTableActions(),
        ];

        // @todo: use $this->filters instead of indexData.
        $indexDataWithoutFilters = $this->indexData($this->request);
        foreach ($indexDataWithoutFilters as $key => $value) {
            if (Str::endsWith($key, 'List')) {
                unset($indexDataWithoutFilters[$key]);
            }
        }

        if ($this->breadcrumbs && ! isset($indexDataWithoutFilters['breadcrumb'])) {
            foreach ($this->breadcrumbs->getListingBreadcrumbs() as $breadcrumb) {
                $indexDataWithoutFilters['breadcrumb'][] = $breadcrumb->toArray();
            }
        }

        $filters = $this->filters()->toFrontendArray($this->repository);

        return array_replace_recursive($data + $options, $indexDataWithoutFilters + $filters);
    }

    /**
     * @param Request $request
     * @return array
     */
    protected function indexData($request)
    {
        return [];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getIndexItems(array $scopes = [], bool $forcePagination = false)
    {
        if (TwillPermissions::enabled() && TwillPermissions::getPermissionModule($this->moduleName)) {
            $scopes += ['accessible' => true];
        }

        $appliedFilters = [];

        $requestFilters = $this->getRequestFilters();

        // Get the applied quick filter..
        if (array_key_exists('status', $requestFilters)) {
            $filter = $this->quickFilters()->filter(
                fn(TwillBaseFilter $filter) => $filter->getQueryString() === $requestFilters['status']
            )->first();

            if ($filter !== null) {
                $appliedFilters[] = $filter;
            }
        }

        unset($requestFilters['status']);

        // Get other filters that need to applied.
        foreach ($requestFilters as $filterKey => $filterValue) {
            $filter = $this->filters()->filter(
                fn(BasicFilter $filter) => $filter->getQueryString() === $filterKey
            )->first();

            if ($filter !== null) {
                $appliedFilters[] = $filter->withFilterValue($filterValue);
            } elseif ($filterKey === 'search') {
                $appliedFilters[] = FreeTextSearch::make()
                    ->searchFor($filterValue)
                    ->searchColumns($this->searchColumns);
            }
        }

        return $this->transformIndexItems(
            $this->repository->get(
                with: $this->indexWith,
                scopes: $scopes,
                orders: $this->orderScope(),
                perPage: $this->request->get('offset') ?? $this->perPage ?? 50,
                forcePagination: $forcePagination,
                appliedFilters: $appliedFilters
            )
        );
    }

    protected function transformIndexItems(Collection|LengthAwarePaginator $items): Collection|LengthAwarePaginator
    {
        return $items;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Collection|TwillModelContract[] $items
     */
    protected function getIndexTableData(Collection|LengthAwarePaginator $items): array
    {
        $translated = $this->moduleHas('translations');

        return $items->map(function (TwillModelContract $item) use ($translated) {
            $columnsData = $this->getTableColumns('index')->getArrayForModel($item);

            $itemIsTrashed = method_exists($item, 'trashed') && $item->trashed();
            $itemCanDelete = $this->getIndexOption('delete', $item) && ($item->canDelete ?? true);
            $canEdit = $this->getIndexOption('edit', $item);
            $canDuplicate = $this->getIndexOption('duplicate');

            $itemId = $this->getItemIdentifier($item);

            $publishable = $item instanceof TwillSchedulableModel;

            return array_replace(
                [
                    'id' => $itemId,
                    'publish_start_date' => $publishable ? $item->publish_start_date : null,
                    'publish_end_date' => $publishable ? $item->publish_end_date : null,
                    'edit' => $canEdit ? $this->getModuleRoute($itemId, 'edit') : null,
                    'duplicate' => $canDuplicate ? $this->getModuleRoute($itemId, 'duplicate') : null,
                    'delete' => $itemCanDelete ? $this->getModuleRoute($itemId, 'destroy') : null,
                ] + ($this->getIndexOption('editInModal') ? [
                    'editInModal' => $this->getModuleRoute($itemId, 'edit'),
                    'updateUrl' => $this->getModuleRoute($itemId, 'update'),
                ] : []) + ($this->getIndexOption('publish') && ($item->canPublish ?? true) ? [
                    'published' => $publishable ? $item->published : null,
                ] : []) + ($this->getIndexOption('feature', $item) && ($item->canFeature ?? true) ? [
                    'featured' => $item->{$this->featureField},
                ] : []) + (($this->getIndexOption('restore', $item) && $itemIsTrashed) ? [
                    'deleted' => true,
                ] : []) + (($this->getIndexOption('forceDelete') && $itemIsTrashed) ? [
                    'destroyable' => true,
                ] : []) + ($translated ? [
                    'languages' => $item->getActiveLanguages(),
                ] : []) + $columnsData,
                $this->indexItemData($item)
            );
        })->toArray();
    }

    protected function indexItemData(TwillModelContract $item)
    {
        return [];
    }

    protected function getItemIdentifier(TwillModelContract $item): null|int|string
    {
        return $item->{$this->identifierColumnKey};
    }

    public function filters(): TableFilters
    {
        $tableFilters = TableFilters::make();

        foreach ($this->indexData($this->request) as $key => $value) {
            if (Str::endsWith($key, 'List')) {
                $queryString = Str::beforeLast($key, 'List');

                if ($filterKey = ($this->filters[$queryString] ?? false)) {
                    if (! $value instanceof Collection) {
                        $value = collect($value)->mapWithKeys(function ($valueLabel) {
                            return [$valueLabel['value'] => $valueLabel['label']];
                        });
                    }

                    $tableFilters->add(
                        BasicFilter::make()
                            ->queryString($queryString)
                            ->options($value)
                            ->apply(function (Builder $builder, mixed $value) use ($filterKey) {
                                $builder->where($filterKey, '=', $value);
                            })
                    );
                }
            }
        }

        return $tableFilters;
    }

    /**
     * The quick filters to apply to the listing table.
     */
    public function quickFilters(): QuickFilters
    {
        return $this->getDefaultQuickFilters();
    }

    protected function getDefaultQuickFilters(): QuickFilters
    {
        $scope = ($this->submodule ? [
            $this->getParentModuleForeignKey() => $this->submoduleParentId,
        ] : []);

        return QuickFilters::make([
            QuickFilter::make()
                ->label(twillTrans('twill::lang.listing.filter.all-items'))
                ->queryString('all')
                ->amount(fn() => $this->repository->getCountByStatusSlug('all', $scope)),
            QuickFilter::make()
                ->label(twillTrans('twill::lang.listing.filter.mine'))
                ->queryString('mine')
                ->scope('mine')
                ->onlyEnableWhen($this->moduleHas('revisions') && $this->getIndexOption('create'))
                ->amount(fn() => $this->repository->getCountByStatusSlug('mine', $scope)),
            QuickFilter::make()
                ->label($this->getTransLabel('listing.filter.published'))
                ->queryString('published')
                ->scope('published')
                ->onlyEnableWhen($this->getIndexOption('publish'))
                ->amount(fn() => $this->repository->getCountByStatusSlug('published', $scope)),
            QuickFilter::make()
                ->label($this->getTransLabel('listing.filter.draft'))
                ->queryString('draft')
                ->scope('draft')
                ->onlyEnableWhen($this->getIndexOption('publish'))
                ->amount(fn() => $this->repository->getCountByStatusSlug('draft', $scope)),
            QuickFilter::make()
                ->label(twillTrans('twill::lang.listing.filter.trash'))
                ->queryString('trash')
                ->scope('onlyTrashed')
                ->onlyEnableWhen($this->getIndexOption('restore'))
                ->amount(fn() => $this->repository->getCountByStatusSlug('trash', $scope)),
        ]);
    }

    /**
     * @param string $moduleName
     * @param string $routePrefix
     * @return array
     */
    protected function getIndexUrls($moduleName, $routePrefix)
    {
        return Collection::make([
            'create',
            'store',
            'publish',
            'bulkPublish',
            'restore',
            'bulkRestore',
            'forceDelete',
            'bulkForceDelete',
            'reorder',
            'feature',
            'bulkFeature',
            'bulkDelete',
        ])->mapWithKeys(function ($endpoint) {
            return [
                $endpoint . 'Url' => $this->getIndexOption($endpoint) ? moduleRoute(
                    $this->moduleName,
                    $this->routePrefix,
                    $endpoint,
                    $this->submodule ? [$this->submoduleParentId] : []
                ) : null,
            ];
        })->toArray();
    }

    /**
     * @param string $option
     * @return bool
     */
    protected function getIndexOption($option, $item = null)
    {
        return once(function () use ($option, $item) {
            $customOptionNamesMapping = [
                'store' => 'create',
            ];
            $option = array_key_exists(
                $option,
                $customOptionNamesMapping
            ) ? $customOptionNamesMapping[$option] : $option;
            $authorized = false;

            if (array_key_exists($option, $this->authorizableOptions)) {
                if (Str::endsWith($this->authorizableOptions[$option], '-module')) {
                    $authorized = $this->user->can($this->authorizableOptions[$option], $this->moduleName);
                } elseif (Str::endsWith($this->authorizableOptions[$option], '-item')) {
                    $authorized = $item ?
                        $this->user->can($this->authorizableOptions[$option], $item) :
                        $this->user->can(
                            Str::replaceLast('-item', '-module', $this->authorizableOptions[$option]),
                            $this->moduleName
                        );
                }
            }

            return ($this->indexOptions[$option] ?? $this->defaultIndexOptions[$option] ?? false) && $authorized;
        });
    }

    protected function getBrowserData(array $scopes = []): array
    {
        if ($this->request->has('except')) {
            $scopes['exceptIds'] = $this->request->get('except');
        }

        $forRepeater = $this->request->get('forRepeater', false) === 'true';

        $items = $this->getBrowserItems($scopes);
        $data = $this->getBrowserTableData($items, $forRepeater);

        return array_replace_recursive(['data' => $data], $this->indexData($this->request));
    }

    protected function getBrowserTableData(Collection|LengthAwarePaginator $items, bool $forRepeater = false): array
    {
        return $items->map(function (TwillModelContract $item) use ($forRepeater) {
            $repeaterFields = [];
            if ($forRepeater) {
                $translatedAttributes = $item->getTranslatedAttributes();
                foreach ($item->getFillable() as $field) {
                    if (in_array($field, $translatedAttributes, true)) {
                        $repeaterFields[$field] = $item->translatedAttribute($field);
                    } else {
                        // @todo: In php 8.1 this is an int by itself. In php8.1 it is not.
                        if ($field === 'published') {
                            $repeaterFields[$field] = (int)$item->{$field};
                            continue;
                        }
                        $repeaterFields[$field] = $item->{$field};
                    }
                }
            }

            return $this->getTableColumns('browser')->getArrayForModelBrowser(
                $item,
                new TableDataContext(
                    $this->titleColumnKey,
                    $this->identifierColumnKey,
                    $this->moduleName,
                    $this->routePrefix,
                    $this->repository->getMorphClass(),
                    $this->moduleHas('medias'),
                    $repeaterFields
                )
            );
        })->toArray();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getBrowserItems(array $scopes = [])
    {
        return $this->getIndexItems($scopes, true);
    }

    protected function getRequestFilters(): array
    {
        if ($this->request->has('search')) {
            return ['search' => $this->request->get('search')];
        }

        $this->applyFiltersDefaultOptions();

        return json_decode($this->request->get('filter'), true) ?? [];
    }

    protected function applyFiltersDefaultOptions(): void
    {
        if ($this->request->has('search')) {
            return;
        }

        $filters = json_decode($this->request->get('filter'), true) ?? [];

        foreach ($this->filtersDefaultOptions as $filterName => $defaultOption) {
            if (! isset($filters[$filterName])) {
                $filters[$filterName] = $defaultOption;
            }
        }

        // Try to figure out which is the default filter. If there is no default filter, we will use the first one.
        if (! isset($filters['status'])) {
            /** @var QuickFilter $quickFilter */
            foreach ($this->quickFilters() as $quickFilter) {
                if ($quickFilter->isDefault()) {
                    $filters['status'] = $quickFilter->getQueryString();
                    break;
                }
            }

            if (! isset($filters['status'])) {
                $filters['status'] = $this->quickFilters()->first()->getQueryString();
            }
        }

        /** @var \A17\Twill\Services\Listings\Filters\BasicFilter $filter */
        foreach ($this->filters() as $filter) {
            if (
                ! isset($filters[$filter->getQueryString()]) &&
                $filter->getDefaultValue() &&
                $filter->getDefaultValue() !== $filter::OPTION_ALL
            ) {
                $filters[$filter->getQueryString()] = $filter->getDefaultValue();
            }
        }

        $this->request->merge(['filter' => json_encode($filters)]);
    }

    protected function orderScope(): array
    {
        $orders = [];
        if ($this->request->has('sortKey') && $this->request->has('sortDir')) {
            if (($key = $this->request->get('sortKey')) === 'name') {
                $sortKey = $this->titleColumnKey;
            } elseif (! empty($key)) {
                $sortKey = $key;
            }

            if (isset($sortKey)) {
                /** @var \A17\Twill\Services\Listings\TableColumn $indexColumn */
                $indexColumn = $this->getIndexTableColumns()->first(function (TableColumn $column) use ($sortKey) {
                    return $column->getKey() === $sortKey;
                });
                if ($indexColumn) {
                    if ($indexColumn->getOrderFunction()) {
                        $orders[$indexColumn->getSortKey()] = [
                            'callback' => $indexColumn->getOrderFunction(),
                            'direction' => $this->request->get('sortDir'),
                        ];
                    } else {
                        $orders[$indexColumn->getSortKey()] = $this->request->get('sortDir');
                    }
                } else {
                    $orders[$sortKey] = $this->request->get('sortDir');
                }
            }
        }

        $defaultOrders = [];

        // don't apply default orders if reorder is enabled
        if (! $this->getIndexOption('reorder')) {
            // We override defaultOrder with our table columns.
            $this->getIndexTableColumns()->each(function (TableColumn $column) use (&$defaultOrders) {
                if ($column->isDefaultSort()) {
                    if ($column->getOrderFunction()) {
                        $defaultOrders[$column->getSortKey()] = [
                            'callback' => $column->getOrderFunction(),
                            'direction' => $column->getDefaultSortDirection(),
                        ];
                    } else {
                        $defaultOrders[$column->getSortKey()] = $column->getDefaultSortDirection();
                    }
                }
            });

            // Add the defaults if they are not in the array yet.
            foreach ($this->defaultOrders ?? [] as $key => $value) {
                if (! isset($defaultOrders[$key])) {
                    $defaultOrders[$key] = $value;
                }
            }
        }

        return $orders + $defaultOrders;
    }

    protected function form(?int $id, ?TwillModelContract $item = null): array
    {
        if (! $item && $id) {
            $item = $this->repository->getById($id, $this->formWith, $this->formWithCount);
        } elseif (! $item && ! $id) {
            $item = $this->repository->newInstance();
        }

        $fullRoutePrefix = config('twill.admin_route_name_prefix') . ($this->routePrefix ? $this->routePrefix . '.' : '') . $this->moduleName . '.';
        $previewRouteName = $fullRoutePrefix . 'preview';
        $restoreRouteName = $fullRoutePrefix . 'restoreRevision';

        $baseUrl = $item->urlWithoutSlug ?? $this->getPermalinkBaseUrl();
        $localizedPermalinkBase = $this->getLocalizedPermalinkBase();

        $itemId = $this->getItemIdentifier($item);

        $data = [
                'item' => $item,
                'moduleName' => $this->moduleName,
                'routePrefix' => $this->routePrefix,
                'titleFormKey' => $this->titleFormKey ?? $this->titleColumnKey,
                'titleFormLabel' => $this->titleFormLabel ?? $this->titleColumnLabel,
                'publish' => $item->canPublish ?? true,
                'publishDate24Hr' => Config::get('twill.publish_date_24h') ?? false,
                'publishDateFormat' => Config::get('twill.publish_date_format') ?? null,
                'publishDateDisplayFormat' => Config::get('twill.publish_date_display_format') ?? null,
                'publishedLabel' => $this->getTransLabel('published'),
                'draftLabel' => $this->getTransLabel('draft'),
                'translate' => $this->moduleHas('translations'),
                'translateTitle' => $this->titleIsTranslatable(),
                'permalink' => $this->getIndexOption('permalink', $item),
                'createWithoutModal' => ! $itemId && $this->getIndexOption('skipCreateModal'),
                'form_fields' => $this->repository->getFormFields($item),
                'baseUrl' => $baseUrl,
                'localizedPermalinkBase' => $localizedPermalinkBase,
                'permalinkPrefix' => $this->getPermalinkPrefix($baseUrl),
                'saveUrl' => $itemId ? $this->getModuleRoute($itemId, 'update') : moduleRoute(
                    $this->moduleName,
                    $this->routePrefix,
                    'store',
                    [$this->submoduleParentId]
                ),
                'editor' => Config::get('twill.enabled.block-editor') && $this->moduleHas(
                    'blocks'
                ) && ! $this->disableEditor,
                'blockPreviewUrl' => Route::has(config('twill.admin_route_name_prefix') . 'blocks.preview') ? URL::route(config('twill.admin_route_name_prefix') . 'blocks.preview') : '#',
                'revisions' => $this->moduleHas('revisions') ? $item->revisionsArray() : null,
                'submitOptions' => $this->getSubmitOptions($item),
                'groupUserMapping' => $this->getGroupUserMapping(),
                'showPermissionFieldset' => $this->getShowPermissionFieldset($item),
            ] + (Route::has($previewRouteName) && $itemId ? [
                'previewUrl' => moduleRoute($this->moduleName, $this->routePrefix, 'preview', [$itemId]),
            ] : [])
            + (Route::has($restoreRouteName) && $itemId ? [
                'restoreUrl' => moduleRoute($this->moduleName, $this->routePrefix, 'restoreRevision', [$itemId]),
            ] : []);

        $form = array_replace_recursive($data, $this->formData($this->request));

        if ($this->breadcrumbs && ! isset($form['breadcrumb'])) {
            foreach ($this->breadcrumbs->getFormBreadcrumbs() as $breadcrumb) {
                $form['breadcrumb'][] = $breadcrumb->toArray();
            }
        }

        View::share('form', $form);

        return $form;
    }

    protected function modalFormData(int|TwillModelContract $modelOrId): array
    {
        if ($modelOrId instanceof TwillModelContract) {
            $item = $modelOrId;
        } else {
            $item = $this->repository->getById($modelOrId, $this->formWith, $this->formWithCount);
        }

        $fields = $this->repository->getFormFields($item);
        $data = [];

        if ($this->moduleHas('translations') && isset($fields['translations'])) {
            foreach ($fields['translations'] as $fieldName => $fieldValue) {
                $data['fields'][] = [
                    'name' => $fieldName,
                    'value' => $fieldValue,
                ];
            }

            $data['languages'] = $item->getActiveLanguages();

            unset($fields['translations']);
        }

        foreach ($fields as $fieldName => $fieldValue) {
            $data['fields'][] = [
                'name' => $fieldName,
                'value' => $fieldValue,
            ];
        }

        return array_replace_recursive($data, $this->formData($this->request));
    }

    /**
     * @param Request $request
     * @return array
     */
    protected function formData($request)
    {
        return [];
    }

    /**
     * @param TwillModelContract $item
     * @return array
     */
    protected function previewData($item)
    {
        return [];
    }

    /**
     * @return \A17\Twill\Http\Requests\Admin\Request
     */
    protected function validateFormRequest()
    {
        $unauthorizedFields = Collection::make($this->fieldsPermissions)->filter(function ($permission, $field) {
            return Auth::guard('twill_users')->user()->cannot($permission);
        })->keys();

        $unauthorizedFields->each(function ($field) {
            $this->request->offsetUnset($field);
        });

        return App::make($this->getFormRequestClass());
    }

    public function getFormRequestClass()
    {
        $prefix = '\Admin';
        if ($this->namespace !== 'A17\Twill') {
            $prefix = "\Twill";
        }

        $request = "$this->namespace\Http\Requests$prefix\\" . $this->modelName . 'Request';

        if (@class_exists($request)) {
            return $request;
        }

        return TwillCapsules::getCapsuleForModel($this->modelName)->getFormRequestClass();
    }

    /**
     * @return string
     */
    protected function getNamespace()
    {
        return $this->namespace ?? Config::get('twill.namespace');
    }

    /**
     * @return string
     */
    protected function getRoutePrefix()
    {
        if ($this->request->route() != null) {
            $routePrefix = ltrim(
                str_replace(Config::get('twill.admin_app_path'), '', $this->request->route()->getPrefix()),
                '/'
            );

            return str_replace('/', '.', $routePrefix);
        }

        return '';
    }

    protected function getModulePermalinkBase(): string
    {
        $base = '';
        $moduleParts = explode('.', $this->moduleName);

        $prev = [];
        foreach ($moduleParts as $index => $name) {
            if (array_key_last($moduleParts) !== $index) {
                $singularName = Str::singular($name);
                $modelName = Str::studly($singularName);
                $modelClass = config('twill.namespace') . '\\Models\\' . $modelName;

                if (! @class_exists($modelClass)) {
                    // First try to construct it based on the last.
                    $modelClass = config('twill.namespace') .
                        '\\Models\\' .
                        implode('', array_merge($prev, [$modelName]));

                    // Last option is to search for a capsule model.
                    if (! class_exists($modelClass)) {
                        $modelClass = TwillCapsules::getCapsuleForModel($modelName)->getModel();
                    }
                }

                $model = (new $modelClass())->findOrFail(request()->route()->parameter($singularName));
                $hasSlug = Arr::has(class_uses($modelClass), HasSlug::class);

                $base .= $name . '/' . ($hasSlug ? $model->slug : $model->id) . '/';

                $prev[] = $modelName;
            } else {
                $base .= $name;
            }
        }

        return $base;
    }

    protected function getModelName(): string
    {
        return $this->modelName ?? ucfirst(Str::singular($this->moduleName));
    }

    protected function getRepository(): ModuleRepository
    {
        return App::make($this->getRepositoryClass($this->modelName));
    }

    protected function getRepositoryClass($model): string
    {
        if (@class_exists($class = "$this->namespace\Repositories\\" . $model . 'Repository')) {
            return $class;
        }

        return TwillCapsules::getCapsuleForModel($model)->getRepositoryClass();
    }

    protected function getViewPrefix(): ?string
    {
        $prefix = "twill.$this->moduleName";

        if (view()->exists("$prefix.form")) {
            return $prefix;
        }

        try {
            return TwillCapsules::getCapsuleForModel($this->modelName)->getViewPrefix();
        } catch (NoCapsuleFoundException $e) {
            return null;
        }
    }

    /**
     * @return string
     */
    protected function getModelTitle()
    {
        return camelCaseToWords($this->modelName);
    }

    /**
     * @return string
     */
    protected function getParentModuleForeignKey()
    {
        $moduleParts = explode('.', $this->moduleName);

        return Str::singular($moduleParts[count($moduleParts) - 2]) . '_id';
    }

    /**
     * @return string
     */
    public function getPermalinkBaseUrl()
    {
        $appUrl = Config::get('app.url');

        if (blank(parse_url($appUrl)['scheme'] ?? null)) {
            $appUrl = $this->request->getScheme() . '://' . $appUrl;
        }

        return $appUrl . '/'
            . ((! $this->withoutLanguageInPermalink && $this->moduleHas('translations')) ? '{language}/' : '')
            . ($this->moduleHas('revisions') ? '{preview}/' : '')
            . (empty($this->getLocalizedPermalinkBase()) ? ($this->permalinkBase ?? $this->getModulePermalinkBase()) : '')
            . (((isset($this->permalinkBase) && empty($this->permalinkBase)) || ! empty(
                $this->getLocalizedPermalinkBase()
            )) ? '' : '/');
    }

    /**
     * @return array
     */
    protected function getLocalizedPermalinkBase(): array
    {
        return [];
    }

    /**
     * @param string $baseUrl
     * @return string
     */
    protected function getPermalinkPrefix($baseUrl)
    {
        return rtrim(str_replace(['http://', 'https://', '{preview}/', '{language}/'], '', $baseUrl), '/') . '/';
    }

    /**
     * @param int $id
     * @param string $action
     * @return string
     */
    protected function getModuleRoute($id, $action)
    {
        return moduleRoute($this->moduleName, $this->routePrefix, $action, [$id]);
    }

    /**
     * @param string $behavior
     * @return bool
     */
    protected function moduleHas($behavior)
    {
        return $this->repository->hasBehavior($behavior);
    }

    /**
     * @return bool
     */
    protected function titleIsTranslatable()
    {
        return $this->repository->isTranslatable(
            $this->titleColumnKey
        );
    }

    /**
     * @param string|null $back_link
     * @param array $params
     * @return void
     */
    protected function setBackLink($back_link = null, $params = [])
    {
        if (! isset($back_link)) {
            if (($back_link = Session::get($this->getBackLinkSessionKey())) == null) {
                $back_link = $this->request->headers->get('referer') ?? moduleRoute(
                    $this->moduleName,
                    $this->routePrefix,
                    'index',
                    $params
                );
            }
        }

        if (! Session::get($this->moduleName . '_retain')) {
            Session::put($this->getBackLinkSessionKey(), $back_link);
        } else {
            Session::put($this->moduleName . '_retain', false);
        }
    }

    /**
     * @param string|null $fallback
     * @param array $params
     * @return string
     */
    protected function getBackLink($fallback = null, $params = [])
    {
        $back_link = Session::get($this->getBackLinkSessionKey(), $fallback);

        return $back_link ?? moduleRoute($this->moduleName, $this->routePrefix, 'index', $params);
    }

    /**
     * @return string
     */
    protected function getBackLinkSessionKey()
    {
        return $this->moduleName . ($this->submodule ? $this->submoduleParentId ?? '' : '') . '_back_link';
    }

    /**
     * @param int $id
     * @param array $params
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function redirectToForm($id, $params = [])
    {
        Session::put($this->moduleName . '_retain', true);

        return Redirect::to(
            moduleRoute(
                $this->moduleName,
                $this->routePrefix,
                'edit',
                array_filter($params) + [Str::singular($this->moduleName) => $id]
            )
        );
    }

    /**
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithSuccess($message)
    {
        return $this->respondWithJson($message, FlashLevel::SUCCESS);
    }

    /**
     * @param string $redirectUrl
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithRedirect($redirectUrl)
    {
        return Response::json([
            'redirect' => $redirectUrl,
        ]);
    }

    /**
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithError($message)
    {
        return $this->respondWithJson($message, FlashLevel::ERROR);
    }

    /**
     * @param string $message
     * @param mixed $variant
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithJson($message, $variant)
    {
        return Response::json([
            'message' => $message,
            'variant' => $variant,
        ]);
    }

    protected function getGroupUserMapping()
    {
        if (config('twill.enabled.permissions-management')) {
            return twillModel('group')::with('users')->get()
                ->mapWithKeys(function ($group) {
                    return [$group->id => $group->users()->pluck('id')->toArray()];
                })->toArray();
        }

        return [];
    }

    /**
     * @param array $input
     * @return void
     */
    protected function fireEvent($input = [])
    {
        fireCmsEvent('cms-module.saved', $input);
    }

    protected function getShowPermissionFieldset($item)
    {
        if (TwillPermissions::enabled()) {
            $permissionModuleName = TwillPermissions::getPermissionModule(getModuleNameByModel($item));

            return $permissionModuleName && ! strpos($permissionModuleName, '.');
        }

        return false;
    }

    /**
     * Get translation key from labels array and attemps to return a translated string.
     *
     * @param string $key
     * @param array $replace
     * @return \Illuminate\Contracts\Translation\Translator|string|array|null
     */
    protected function getTransLabel($key, $replace = [])
    {
        return twillTrans(Arr::has($this->labels, $key) ? Arr::get($this->labels, $key) : $key, $replace);
    }

    public function getForm(TwillModelContract $model): Form
    {
        return new Form();
    }

    public function getSideFieldsets(TwillModelContract $model): Form
    {
        return new Form();
    }
}
