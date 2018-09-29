<?php

namespace A17\Twill\Http\Controllers\Admin;

use A17\Twill\Helpers\FlashLevel;
use Auth;
use Event;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Route;
use Session;

abstract class ModuleController extends Controller
{

    protected $app;

    protected $request;

    protected $routePrefix;

    protected $moduleName;

    protected $modelName;

    protected $repository;

    /*
     * Options of the index view
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
        'delete' => true,
        'bulkDelete' => true,
        'reorder' => false,
        'permalink' => true,
        'bulkEdit' => true,
        'editInModal' => false,
    ];

    /*
     * Relations to eager load for the index view
     */
    protected $indexWith = [];

    /*
     * Relations to eager load for the form view
     */
    protected $formWith = [];

    /*
     * Relation count to eager load for the form view
     */
    protected $formWithCount = [];

    /*
     * Additional filters for the index view
     * To automatically have your filter added to the index view use the following convention:
     * suffix the key containing the list of items to show in the filter by 'List' and
     * name it the same as the filter you defined in this array.
     * Example: 'fCategory' => 'category_id' here and 'fCategoryList' in indexData()
     * By default, this will run a where query on the category_id column with the value
     * of fCategory if found in current request parameters. You can intercept this behavior
     * from your repository in the filter() function.
     */
    protected $filters = [];

    /*
     * Default orders for the index view
     */
    protected $defaultOrders = [
        'created_at' => 'desc',
    ];

    protected $perPage = 20;

    /*
     * Name of the index column to use as name column
     */
    protected $titleColumnKey = 'title';

    /*
     * Attribute to use as title in forms
     */
    protected $titleFormKey;

    /*
     * Feature field name if the controller is using the feature route (defaults to "featured")
     */
    protected $featureField = 'featured';

    /*
     * Indicates if this module is edited through a parent module
     */
    protected $submodule = false;
    protected $submoduleParentId = null;

    /*
     * Can be used in child classes to disable the content editor (full screen block editor)
     */
    protected $disableEditor = false;

    public function __construct(Application $app, Request $request)
    {
        parent::__construct();
        $this->app = $app;
        $this->request = $request;

        $this->setMiddlewarePermission();

        $this->modelName = $this->getModelName();
        $this->routePrefix = $this->getRoutePrefix();
        $this->namespace = $this->getNamespace();
        $this->repository = $this->getRepository();
        $this->viewPrefix = $this->getViewPrefix();
        $this->modelTitle = $this->getModelTitle();

        /*
         * Default filters for the index view
         * By default, the search field will run a like query on the title field
         */
        if (!isset($this->defaultFilters)) {
            $this->defaultFilters = [
                'search' => ($this->moduleHas('translations') ? '' : '%') . $this->titleColumnKey,
            ];
        }

        /*
         * Available columns of the index view
         */
        if (!isset($this->indexColumns)) {
            $this->indexColumns = [
                $this->titleColumnKey => [
                    'title' => ucfirst($this->titleColumnKey),
                    'field' => $this->titleColumnKey,
                    'sort' => true,
                ],
            ];
        }

        /*
         * Available columns of the browser view
         */
        if (!isset($this->browserColumns)) {
            $this->browserColumns = [
                $this->titleColumnKey => [
                    'title' => ucfirst($this->titleColumnKey),
                    'field' => $this->titleColumnKey,
                ],
            ];
        }
    }

    protected function setMiddlewarePermission()
    {
        $this->middleware('can:list', ['only' => ['index', 'show']]);
        $this->middleware('can:edit', ['only' => ['store', 'edit', 'update']]);
        $this->middleware('can:publish', ['only' => ['publish', 'feature', 'bulkPublish', 'bulkFeature']]);
        $this->middleware('can:reorder', ['only' => ['reorder']]);
        $this->middleware('can:delete', ['only' => ['destroy', 'bulkDelete', 'restore', 'bulkRestore', 'restoreRevision']]);
    }

    public function index($parentModuleId = null)
    {
        $this->submodule = isset($parentModuleId);
        $this->submoduleParentId = $parentModuleId;

        $indexData = $this->getIndexData($this->submodule ? [
            $this->getParentModuleForeignKey() => $this->submoduleParentId,
        ] : []);

        if ($this->request->ajax()) {
            return $indexData + ['replaceUrl' => true];
        }

        if ($this->request->has('openCreate') && request('openCreate')) {
            $indexData += ['openCreate' => true];
        }

        $view = collect([
            "$this->viewPrefix.index",
            "twill::$this->moduleName.index",
            "twill::layouts.listing",
        ])->first(function ($view) {
            return view()->exists($view);
        });

        return view($view, $indexData);
    }

    public function browser()
    {
        return response()->json($this->getBrowserData());
    }

    public function store($parentModuleId = null)
    {
        $input = $this->validateFormRequest()->all();
        $optionalParent = $parentModuleId ? [$this->getParentModuleForeignKey() => $parentModuleId] : [];

        $item = $this->repository->create($input + $optionalParent);

        activity()->performedOn($item)->log('created');

        $this->fireEvent($input);

        Session::put($this->moduleName . '_retain', true);

        if ($this->getIndexOption('editInModal')) {
            return $this->respondWithSuccess('Content saved. All good!');
        }

        return $this->respondWithRedirect(moduleRoute(
            $this->moduleName,
            $this->routePrefix,
            'edit',
            array_filter([$parentModuleId]) + ['id' => $item->id]
        ));
    }

    public function show($id, $submoduleId = null)
    {
        if ($this->getIndexOption('editInModal')) {
            return redirect(moduleRoute($this->moduleName, $this->routePrefix, 'index'));
        }

        return $this->redirectToForm($submoduleId ?? $id);
    }

    public function edit($id, $submoduleId = null)
    {
        $this->submodule = isset($submoduleId);
        $this->submoduleParentId = $id;

        if ($this->getIndexOption('editInModal')) {
            return $this->request->ajax()
            ? response()->json($this->modalFormData($submodule ?? $id))
            : redirect(moduleRoute($this->moduleName, $this->routePrefix, 'index'));
        }

        $this->setBackLink();

        $view = collect([
            "$this->viewPrefix.form",
            "twill::$this->moduleName.form",
            "twill::layouts.form",
        ])->first(function ($view) {
            return view()->exists($view);
        });

        return view($view, $this->form($submoduleId ?? $id));
    }

    public function update($id, $submoduleId = null)
    {
        $this->submodule = isset($submoduleId);
        $this->submoduleParentId = $id;

        $item = $this->repository->getById($submoduleId ?? $id);
        $input = $this->request->all();

        if (isset($input['cmsSaveType']) && $input['cmsSaveType'] === 'cancel') {
            return $this->respondWithRedirect(moduleRoute(
                $this->moduleName,
                $this->routePrefix,
                'edit',
                ['id' => $id]
            ));
        } else {
            $formRequest = $this->validateFormRequest();

            $this->repository->update($submoduleId ?? $id, $formRequest->all());

            activity()->performedOn($item)->log('updated');

            $this->fireEvent();

            if (isset($input['cmsSaveType'])) {
                if (ends_with($input['cmsSaveType'], '-close')) {
                    return $this->respondWithRedirect($this->getBackLink());
                } elseif (ends_with($input['cmsSaveType'], '-new')) {
                    return $this->respondWithRedirect(moduleRoute(
                        $this->moduleName,
                        $this->routePrefix,
                        'index',
                        ['openCreate' => true]
                    ));
                } elseif ($input['cmsSaveType'] === 'restore') {
                    session()->flash('status', "Revision restored.");

                    return $this->respondWithRedirect(moduleRoute(
                        $this->moduleName,
                        $this->routePrefix,
                        'edit',
                        ['id' => $id]
                    ));
                }
            }

            if ($this->moduleHas('revisions')) {
                return response()->json([
                    'message' => 'Content saved. All good!',
                    'variant' => FlashLevel::SUCCESS,
                    'revisions' => $item->revisionsArray(),
                ]);
            }

            return $this->respondWithSuccess('Content saved. All good!');
        }
    }

    public function preview($id)
    {
        if (request()->has('revisionId')) {
            $item = $this->repository->previewForRevision($id, request('revisionId'));
        } else {
            $formRequest = $this->validateFormRequest();
            $item = $this->repository->preview($id, $formRequest->all());
        }

        if (request()->has('activeLanguage')) {
            $this->app->setLocale(request('activeLanguage'));
        }

        $previewView = $this->previewView ?? (config('twill.frontend.views_path', 'site') . '.' . str_singular($this->moduleName));

        return view()->exists($previewView) ? view($previewView, array_replace([
            'item' => $item,
        ], $this->previewData($item))) : view('twill::errors.preview', [
            'moduleName' => str_singular($this->moduleName),
        ]);
    }

    public function restoreRevision($id)
    {
        if (request()->has('revisionId')) {
            $item = $this->repository->previewForRevision($id, request('revisionId'));
            $item->id = $id;
            $item->cmsRestoring = true;
        } else {
            abort(404);
        }

        $this->setBackLink();

        $view = collect([
            "$this->viewPrefix.form",
            "twill::$this->moduleName.form",
            "twill::layouts.form",
        ])->first(function ($view) {
            return view()->exists($view);
        });

        $revision = $item->revisions()->where('id', request('revisionId'))->first();
        $date = $revision->created_at->toDayDateTimeString();

        session()->flash('restoreMessage', "You are currently editing an older revision of this content (saved by $revision->byUser on $date). Make changes if needed and click restore to save a new revision.");

        return view($view, $this->form($id, $item));
    }

    public function publish()
    {
        try {
            if ($this->repository->updateBasic(request('id'), [
                'published' => !request('active'),
            ])) {
                activity()->performedOn(
                    $this->repository->getById(request('id'))
                )->log(
                    (request('active') ? 'un' : '') . 'published'
                );

                $this->fireEvent();

                return $this->respondWithSuccess(
                    $this->modelTitle . ' ' . (request('active') ? 'un' : '') . 'published!'
                );
            }
        } catch (\Exception $e) {
            \Log::error($e);
        }

        return $this->respondWithError(
            $this->modelTitle . ' was not published. Something wrong happened!'
        );
    }

    public function bulkPublish()
    {
        try {
            if ($this->repository->updateBasic(explode(',', request('ids')), [
                'published' => request('publish'),
            ])) {
                $this->fireEvent();

                return $this->respondWithSuccess(
                    $this->modelTitle . ' items ' . (request('publish') ? '' : 'un') . 'published!'
                );
            }
        } catch (\Exception $e) {
            \Log::error($e);
        }

        return $this->respondWithError(
            $this->modelTitle . ' items were not published. Something wrong happened!'
        );
    }

    public function destroy($id, $submoduleId = null)
    {
        $item = $this->repository->getById($id);
        if ($this->repository->delete($submoduleId ?? $id)) {
            $this->fireEvent();
            activity()->performedOn($item)->log('deleted');
            return $this->respondWithSuccess($this->modelTitle . ' moved to trash!');
        }

        return $this->respondWithError($this->modelTitle . ' was not moved to trash. Something wrong happened!');
    }

    public function bulkDelete()
    {
        if ($this->repository->bulkDelete(explode(',', request('ids')))) {
            $this->fireEvent();
            return $this->respondWithSuccess($this->modelTitle . ' items moved to trash!');
        }

        return $this->respondWithError($this->modelTitle . ' items were not moved to trash. Something wrong happened!');
    }

    public function restore()
    {
        if ($this->repository->restore(request('id'))) {
            $this->fireEvent();
            activity()->performedOn($this->repository->getById(request('id')))->log('restored');
            return $this->respondWithSuccess($this->modelTitle . ' restored!');
        }

        return $this->respondWithError($this->modelTitle . ' was not restored. Something wrong happened!');
    }

    public function bulkRestore()
    {
        if ($this->repository->bulkRestore(explode(',', request('ids')))) {
            $this->fireEvent();
            return $this->respondWithSuccess($this->modelTitle . ' items restored!');
        }

        return $this->respondWithError($this->modelTitle . ' items were not restored. Something wrong happened!');
    }

    public function feature()
    {
        if (($id = request('id'))) {
            $featuredField = request('featureField') ?? $this->featureField;
            $featured = !request('active');

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
                (request('active') ? 'un' : '') . 'featured'
            );

            $this->fireEvent();
            return $this->respondWithSuccess($this->modelTitle . ' ' . (request('active') ? 'un' : '') . 'featured!');
        }

        return $this->respondWithError($this->modelTitle . ' was not featured. Something wrong happened!');
    }

    public function bulkFeature()
    {
        if (($ids = explode(',', request('ids')))) {
            $featuredField = request('featureField') ?? $this->featureField;
            $featured = request('feature') ?? true;
            // we don't need to check if unique feature since bulk operation shouldn't be allowed in this case
            $this->repository->updateBasic($ids, [$featuredField => $featured]);
            $this->fireEvent();
            return $this->respondWithSuccess($this->modelTitle . ' items ' . (request('feature') ? '' : 'un') . 'featured!');
        }

        return $this->respondWithError($this->modelTitle . ' items were not featured. Something wrong happened!');
    }

    public function reorder()
    {
        if (($values = request('ids')) && !empty($values)) {
            $this->repository->setNewOrder($values);
            $this->fireEvent();
            return $this->respondWithSuccess($this->modelTitle . ' order changed!');
        }

        return $this->respondWithError($this->modelTitle . ' order was not changed. Something wrong happened!');
    }

    public function tags()
    {
        $query = $this->request->input('q');
        $tags = $this->repository->getTags($query);

        return response()->json(['items' => $tags->map(function ($tag) {
            return $tag->name;
        })], 200);
    }

    protected function getIndexData($prependScope = [])
    {
        $scopes = $this->filterScope($prependScope);
        $items = $this->getIndexItems($scopes);

        $data = [
            'tableData' => $this->getIndexTableData($items),
            'tableColumns' => $this->getIndexTableColumns($items),
            'tableMainFilters' => $this->getIndexTableMainFilters($items),
            'filters' => json_decode($this->request->get('filter'), true) ?? [],
            'hiddenFilters' => array_keys(array_except($this->filters, array_keys($this->defaultFilters))),
            'maxPage' => method_exists($items, 'lastPage') ? $items->lastPage() : 1,
            'defaultMaxPage' => method_exists($items, 'total') ? ceil($items->total() / $this->perPage) : 1,
            'offset' => method_exists($items, 'perPage') ? $items->perPage() : count($items),
            'defaultOffset' => $this->perPage,
        ] + $this->getIndexUrls($this->moduleName, $this->routePrefix);

        $baseUrl = $this->getPermalinkBaseUrl();

        $options = [
            'moduleName' => $this->moduleName,
            'reorder' => $this->getIndexOption('reorder'),
            'create' => $this->getIndexOption('create'),
            'translate' => $this->moduleHas('translations'),
            'permalink' => $this->getIndexOption('permalink'),
            'bulkEdit' => $this->getIndexOption('bulkEdit'),
            'titleFormKey' => $this->titleFormKey ?? $this->titleColumnKey,
            'baseUrl' => $baseUrl,
            'permalinkPrefix' => $this->getPermalinkPrefix($baseUrl),
        ];

        return array_replace_recursive($data + $options, $this->indexData($this->request));
    }

    protected function indexData($request)
    {
        return [];
    }

    protected function getIndexItems($scopes = [], $forcePagination = false)
    {
        return $this->transformIndexItems($this->repository->get(
            $this->indexWith,
            $scopes,
            $this->orderScope(),
            request('offset') ?? $this->perPage ?? 50,
            $forcePagination
        ));
    }

    protected function transformIndexItems($items)
    {
        return $items;
    }

    protected function getIndexTableData($items)
    {
        $translated = $this->moduleHas('translations');
        return $items->map(function ($item) use ($translated) {
            $columnsData = collect($this->indexColumns)->mapWithKeys(function ($column) use ($item) {
                return $this->getItemColumnData($item, $column);
            })->toArray();

            $name = $columnsData[$this->titleColumnKey];

            if (empty($name)) {
                if ($this->moduleHas('translations')) {
                    $fallBackTranslation = $item->translations()->where('active', true)->first();

                    if (isset($fallBackTranslation->{$this->titleColumnKey})) {
                        $name = $fallBackTranslation->{$this->titleColumnKey};
                    }
                }

                $name = $name ?? ('Missing ' . $this->titleColumnKey);
            }

            unset($columnsData[$this->titleColumnKey]);

            $itemIsTrashed = method_exists($item, 'trashed') && $item->trashed();
            $itemCanDelete = $this->getIndexOption('delete') && ($item->canDelete ?? true);
            $canEdit = $this->getIndexOption('edit');

            return array_replace([
                'id' => $item->id,
                'name' => $name,
                'publish_start_date' => $item->publish_start_date,
                'publish_end_date' => $item->publish_end_date,
                'edit' => $canEdit ? $this->getModuleRoute($item->id, 'edit') : null,
                'delete' => ($canEdit && $itemCanDelete) ? $this->getModuleRoute($item->id, 'destroy') : null,
            ] + ($this->getIndexOption('editInModal') ? [
                'editInModal' => $this->getModuleRoute($item->id, 'edit'),
                'updateUrl' => $this->getModuleRoute($item->id, 'update'),
            ] : []) + ($this->getIndexOption('publish') && ($item->canPublish ?? true) ? [
                'published' => $item->published,
            ] : []) + ($this->getIndexOption('feature') && ($item->canFeature ?? true) ? [
                'featured' => $item->{$this->featureField},
            ] : []) + (($this->getIndexOption('restore') && $itemIsTrashed) ? [
                'deleted' => true,
            ] : []) + ($translated ? [
                'languages' => $item->getActiveLanguages(),
            ] : []) + $columnsData, $this->indexItemData($item));
        })->toArray();
    }

    protected function indexItemData($item)
    {
        return [];
    }

    protected function getItemColumnData($item, $column)
    {
        if (isset($column['thumb']) && $column['thumb']) {
            if (isset($column['present']) && $column['present']) {
                return [
                    'thumbnail' => $item->presentAdmin()->{$column['presenter']},
                ];
            } else {
                $variant = isset($column['variant']);
                $role = $variant ? $column['variant']['role'] : head(array_keys($item->mediasParams));
                $crop = $variant ? $column['variant']['crop'] : head(array_keys(head($item->mediasParams)));
                $params = $variant && isset($column['variant']['params'])
                ? $column['variant']['params']
                : ['w' => 80, 'h' => 80, 'fit' => 'crop'];

                return [
                    'thumbnail' => $item->cmsImage($role, $crop, $params),
                ];
            }
        }

        if (isset($column['nested']) && $column['nested']) {
            $field = $column['nested'];
            $nestedCount = $item->{$column['nested']}->count();
            $value = '<a href="';
            $value .= moduleRoute("$this->moduleName.$field", $this->routePrefix, 'index', [$item->id]);
            $value .= '">' . $nestedCount . " " . (strtolower($nestedCount > 1
                ? str_plural($column['title'])
                : str_singular($column['title']))) . '</a>';
        } else {
            $field = $column['field'];
            $value = $item->$field;
        }

        if (isset($column['relationship'])) {
            $field = $column['relationship'] . ucfirst($column['field']);
            $value = array_get($item, "{$column['relationship']}.{$column['field']}");
        } elseif (isset($column['present']) && $column['present']) {
            $value = $item->presentAdmin()->{$column['field']};
        }

        return [
            "$field" => $value,
        ];
    }

    protected function getIndexTableColumns($items)
    {
        $tableColumns = [];
        $visibleColumns = request('columns') ?? false;

        if (isset(array_first($this->indexColumns)['thumb'])
            && array_first($this->indexColumns)['thumb']
        ) {
            array_push($tableColumns, [
                'name' => 'thumbnail',
                'label' => 'Thumbnail',
                'visible' => $visibleColumns ? in_array('thumbnail', $visibleColumns) : true,
                'optional' => true,
                'sortable' => false,
            ]);
            array_shift($this->indexColumns);
        }

        if ($this->getIndexOption('feature')) {
            array_push($tableColumns, [
                'name' => 'featured',
                'label' => 'Featured',
                'visible' => true,
                'optional' => false,
                'sortable' => false,
            ]);
        }

        if ($this->getIndexOption('publish')) {
            array_push($tableColumns, [
                'name' => 'published',
                'label' => 'Published',
                'visible' => true,
                'optional' => false,
                'sortable' => false,
            ]);
        }

        array_push($tableColumns, [
            'name' => 'name',
            'label' => $this->indexColumns[$this->titleColumnKey]['title'] ?? 'Name',
            'visible' => true,
            'optional' => false,
            'sortable' => $this->getIndexOption('reorder') ? false : ($this->indexColumns[$this->titleColumnKey]['sort'] ?? false),
        ]);

        unset($this->indexColumns[$this->titleColumnKey]);

        foreach ($this->indexColumns as $column) {
            $columnName = isset($column['relationship'])
            ? $column['relationship'] . ucfirst($column['field'])
            : (isset($column['nested']) ? $column['nested'] : $column['field']);

            array_push($tableColumns, [
                'name' => $columnName,
                'label' => $column['title'],
                'visible' => $visibleColumns ? in_array($columnName, $visibleColumns) : ($column['visible'] ?? true),
                'optional' => $column['optional'] ?? true,
                'sortable' => $this->getIndexOption('reorder') ? false : ($column['sort'] ?? false),
                'html' => $column['html'] ?? false,
            ]);
        }

        if ($this->moduleHas('translations')) {
            array_push($tableColumns, [
                'name' => 'languages',
                'label' => 'Languages',
                'visible' => $visibleColumns ? in_array('languages', $visibleColumns) : true,
                'optional' => true,
                'sortable' => false,
            ]);
        }

        return $tableColumns;
    }

    protected function getIndexTableMainFilters($items, $scopes = [])
    {
        $statusFilters = [];

        $scope = ($this->submodule ? [
            $this->getParentModuleForeignKey() => $this->submoduleParentId,
        ] : []) + $scopes;

        array_push($statusFilters, [
            'name' => 'All items',
            'slug' => 'all',
            'number' => $this->repository->getCountByStatusSlug('all', $scope),
        ]);

        if ($this->moduleHas('revisions') && $this->getIndexOption('create')) {
            array_push($statusFilters, [
                'name' => 'Mine',
                'slug' => 'mine',
                'number' => $this->repository->getCountByStatusSlug('mine', $scope),
            ]);
        }

        if ($this->getIndexOption('publish')) {
            array_push($statusFilters, [
                'name' => 'Published',
                'slug' => 'published',
                'number' => $this->repository->getCountByStatusSlug('published', $scope),
            ], [
                'name' => 'Draft',
                'slug' => 'draft',
                'number' => $this->repository->getCountByStatusSlug('draft', $scope),
            ]);
        }

        if ($this->getIndexOption('restore')) {
            array_push($statusFilters, [
                'name' => 'Trash',
                'slug' => 'trash',
                'number' => $this->repository->getCountByStatusSlug('trash', $scope),
            ]);
        }

        return $statusFilters;
    }

    protected function getIndexUrls($moduleName, $routePrefix)
    {
        return collect([
            'store',
            'publish',
            'bulkPublish',
            'restore',
            'bulkRestore',
            'reorder',
            'feature',
            'bulkFeature',
            'bulkDelete',
        ])->mapWithKeys(function ($endpoint) use ($moduleName, $routePrefix) {
            return [
                $endpoint . 'Url' => $this->getIndexOption($endpoint) ? moduleRoute(
                    $this->moduleName, $this->routePrefix, $endpoint,
                    $this->submodule ? [$this->submoduleParentId] : []
                ) : null,
            ];
        })->toArray();
    }

    protected function getIndexOption($option)
    {
        return once(function () use ($option) {
            $customOptionNamesMapping = [
                'store' => 'create',
            ];

            $option = array_key_exists($option, $customOptionNamesMapping) ? $customOptionNamesMapping[$option] : $option;

            $authorizableOptions = [
                'create' => 'edit',
                'edit' => 'edit',
                'publish' => 'publish',
                'feature' => 'feature',
                'reorder' => 'reorder',
                'delete' => 'delete',
                'restore' => 'delete',
                'bulkPublish' => 'publish',
                'bulkRestore' => 'delete',
                'bulkFeature' => 'feature',
                'bulkDelete' => 'delete',
                'bulkEdit' => 'edit',
                'editInModal' => 'edit',
            ];

            $authorized = array_key_exists($option, $authorizableOptions) ? auth('twill_users')->user()->can($authorizableOptions[$option]) : true;
            return ($this->indexOptions[$option] ?? $this->defaultIndexOptions[$option] ?? false) && $authorized;
        });
    }

    protected function getBrowserData($prependScope = [])
    {
        if (request()->has('except')) {
            $prependScope['exceptIds'] = request('except');
        }

        $scopes = $this->filterScope($prependScope);
        $items = $this->getBrowserItems($scopes);
        $data = $this->getBrowserTableData($items);

        return array_replace_recursive(['data' => $data], $this->indexData($this->request));
    }

    protected function getBrowserTableData($items)
    {
        $withImage = $this->moduleHas('medias');

        return $items->map(function ($item) {
            $columnsData = collect($this->browserColumns)->mapWithKeys(function ($column) use ($item) {
                return $this->getItemColumnData($item, $column);
            })->toArray();

            $name = $columnsData[$this->titleColumnKey];
            unset($columnsData[$this->titleColumnKey]);

            return [
                'id' => $item->id,
                'name' => $name,
                'edit' => moduleRoute($this->moduleName, $this->routePrefix, 'edit', $item->id),
            ] + $columnsData;
        })->toArray();
    }

    protected function getBrowserItems($scopes = [])
    {
        return $this->getIndexItems($scopes, true);
    }

    protected function filterScope($prepend = [])
    {
        $scope = [];

        $requestFilters = $this->getRequestFilters();

        $this->filters = array_merge($this->filters, $this->defaultFilters);

        if (array_key_exists('status', $requestFilters)) {
            switch ($requestFilters['status']) {
                case 'published':
                    $scope['published'] = true;
                    break;
                case 'draft':
                    $scope['draft'] = true;
                    break;
                case 'trash':
                    $scope['onlyTrashed'] = true;
                    break;
                case 'mine':
                    $scope['mine'] = true;
                    break;
            }

            unset($requestFilters['status']);
        }

        foreach ($this->filters as $key => $field) {
            if (array_key_exists($key, $requestFilters)) {
                $value = $requestFilters[$key];
                if ($value == 0 || !empty($value)) {
                    // add some syntaxic sugar to scope the same filter on multiple columns
                    $fieldSplitted = explode('|', $field);
                    if (count($fieldSplitted) > 1) {
                        $requestValue = $requestFilters[$key];
                        collect($fieldSplitted)->each(function ($scopeKey) use (&$scope, $requestValue) {
                            $scope[$scopeKey] = $requestValue;
                        });
                    } else {
                        $scope[$field] = $requestFilters[$key];
                    }
                }
            }
        }

        return $prepend + $scope;
    }

    protected function getRequestFilters()
    {
        if (request()->has('search')) {
            return ['search' => request('search')];
        }

        return json_decode($this->request->get('filter'), true) ?? [];
    }

    protected function orderScope()
    {
        $orders = [];
        if ($this->request->has('sortKey') && $this->request->has('sortDir')) {
            if (($key = $this->request->get('sortKey')) == 'name') {
                $sortKey = $this->titleColumnKey;
            } elseif (!empty($key)) {
                $sortKey = $key;
            }

            if (isset($sortKey)) {
                $orders[$this->indexColumns[$sortKey]['sortKey'] ?? $sortKey] = $this->request->get('sortDir');
            }
        }

        // don't apply default orders if reorder is enabled
        $reorder = $this->getIndexOption('reorder');
        $defaultOrders = ($reorder ? [] : ($this->defaultOrders ?? []));

        return $orders + $defaultOrders;
    }

    protected function form($id, $item = null)
    {
        $item = $item ?? $this->repository->getById($id, $this->formWith, $this->formWithCount);

        $fullRoutePrefix = 'admin.' . ($this->routePrefix ? $this->routePrefix . '.' : '') . $this->moduleName . '.';
        $previewRouteName = $fullRoutePrefix . 'preview';
        $restoreRouteName = $fullRoutePrefix . 'restoreRevision';

        $baseUrl = $item->urlWithoutSlug ?? $this->getPermalinkBaseUrl();

        $data = [
            'item' => $item,
            'moduleName' => $this->moduleName,
            'routePrefix' => $this->routePrefix,
            'titleFormKey' => $this->titleFormKey ?? $this->titleColumnKey,
            'translate' => $this->moduleHas('translations'),
            'permalink' => $this->getIndexOption('permalink'),
            'form_fields' => $this->repository->getFormFields($item),
            'baseUrl' => $baseUrl,
            'permalinkPrefix' => $this->getPermalinkPrefix($baseUrl),
            'saveUrl' => $this->getModuleRoute($item->id, 'update'),
            'editor' => $this->moduleHas('revisions') && $this->moduleHas('blocks') && !$this->disableEditor,
            'blockPreviewUrl' => route('admin.blocks.preview'),
            'revisions' => $this->moduleHas('revisions') ? $item->revisionsArray() : null,
        ] + (Route::has($previewRouteName) ? [
            'previewUrl' => moduleRoute($this->moduleName, $this->routePrefix, 'preview', $item->id),
        ] : [])
             + (Route::has($restoreRouteName) ? [
            'restoreUrl' => moduleRoute($this->moduleName, $this->routePrefix, 'restoreRevision', $item->id),
        ] : []);

        return array_replace_recursive($data, $this->formData($this->request));
    }

    protected function modalFormData($id)
    {
        $item = $this->repository->getById($id, $this->formWith, $this->formWithCount);
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

    protected function formData($request)
    {
        return [];
    }

    protected function previewData($item)
    {
        return [];
    }

    protected function validateFormRequest()
    {
        return $this->app->make("$this->namespace\Http\Requests\Admin\\" . $this->modelName . "Request");
    }

    protected function getNamespace()
    {
        return $this->namespace ?? config('twill.namespace');
    }

    protected function getRoutePrefix()
    {
        if ($this->request->route() != null) {
            $routePrefix = ltrim(str_replace(config('twill.admin_app_path'), '', $this->request->route()->getPrefix()), "/");
            return str_replace("/", ".", $routePrefix);
        }

        return '';
    }

    protected function getModelName()
    {
        return $this->modelName ?? ucfirst(str_singular($this->moduleName));
    }

    protected function getRepository()
    {
        return $this->app->make("$this->namespace\Repositories\\" . $this->modelName . "Repository");
    }

    protected function getViewPrefix()
    {
        return "admin.$this->moduleName";
    }

    protected function getModelTitle()
    {
        return camelCaseToWords($this->modelName);
    }

    protected function getParentModuleForeignKey()
    {
        return str_singular(explode('.', $this->moduleName)[0]) . '_id';
    }

    protected function getPermalinkBaseUrl()
    {
        return request()->getScheme() . '://' . config('app.url') . '/'
            . ($this->moduleHas('translations') ? '{language}/' : '')
            . ($this->moduleHas('revisions') ? '{preview}/' : '')
            . ($this->permalinkBase ?? $this->moduleName)
            . (isset($this->permalinkBase) && empty($this->permalinkBase) ? '' : '/');
    }

    protected function getPermalinkPrefix($baseUrl)
    {
        return rtrim(str_replace(['http://', 'https://', '{preview}/', '{language}/'], '', $baseUrl), "/") . '/';
    }

    protected function getModuleRoute($id, $action)
    {
        return moduleRoute(
            $this->moduleName,
            $this->routePrefix,
            $action,
            array_merge($this->submodule ? [$this->submoduleParentId] : [], [$id])
        );
    }

    protected function moduleHas($behavior)
    {
        return classHasTrait($this->repository, 'A17\Twill\Repositories\Behaviors\Handle' . ucfirst($behavior));
    }

    protected function setBackLink($back_link = null, $params = [])
    {
        if (!isset($back_link)) {
            if (($back_link = Session::get($this->getBackLinkSessionKey())) == null) {
                $back_link = $this->request->headers->get('referer') ?? moduleRoute(
                    $this->moduleName,
                    $this->routePrefix,
                    'index',
                    $params
                );
            }
        }

        if (!Session::get($this->moduleName . '_retain')) {
            Session::put($this->getBackLinkSessionKey(), $back_link);
        } else {
            Session::put($this->moduleName . '_retain', false);
        }
    }

    protected function getBackLink($fallback = null, $params = [])
    {
        $back_link = Session::get($this->getBackLinkSessionKey(), $fallback);
        return $back_link ?? moduleRoute($this->moduleName, $this->routePrefix, 'index', $params);
    }

    protected function getBackLinkSessionKey()
    {
        return $this->moduleName . ($this->submodule ? $this->submoduleParentId ?? '' : '') . '_back_link';
    }

    protected function redirectToForm($id, $params = [])
    {
        Session::put($this->moduleName . '_retain', true);

        return redirect(moduleRoute(
            $this->moduleName,
            $this->routePrefix,
            'edit',
            array_filter($params) + ['id' => $id]
        ));
    }

    protected function respondWithSuccess($message)
    {
        return $this->respondWithJson($message, FlashLevel::SUCCESS);
    }

    protected function respondWithRedirect($redirectUrl)
    {
        return response()->json([
            'redirect' => $redirectUrl,
        ]);
    }

    protected function respondWithError($message)
    {
        return $this->respondWithJson($message, FlashLevel::ERROR);
    }

    protected function respondWithJson($message, $variant)
    {
        return response()->json([
            'message' => $message,
            'variant' => $variant,
        ]);
    }

    protected function fireEvent($input = [])
    {
        Event::fire('cms-module.saved', ['cms-module.saved', $input]);
    }
}
