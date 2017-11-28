<?php

namespace A17\CmsToolkit\Http\Controllers\Admin;

use A17\CmsToolkit\Helpers\FlashLevel;
use Auth;
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
     * Available columns of the index view
     */
    protected $indexColumns = [
        'title' => [
            'title' => 'Title',
            'field' => 'title',
            'sort' => true,
        ],
    ];

    /*
     * Available columns of the browser view
     */
    protected $browserColumns = [
        'title' => [
            'title' => 'Title',
            'field' => 'title',
        ],
    ];

    /*
     * Options of the index view
     */
    protected $defaultIndexOptions = [
        'create' => true,
        'publish' => true,
        'bulkPublish' => true,
        'feature' => false,
        'bulkFeature' => false,
        'restore' => true,
        'bulkRestore' => true,
        'bulkDelete' => true,
        'reorder' => false,
        'permalink' => true,
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
     * Default filters for the index view
     * By default, the search field will run a like query on the title field
     * and is giving you the ability to specify more column to search in
     * using the searchIn function in your repository filter() function.
     */
    protected $defaultFilters = [
        'search' => 'title|search',
    ];

    /*
     * Default orders for the index view
     */
    protected $defaultOrders = [
        'title' => 'asc',
    ];

    /*
     * Feature field name if the controller is using the feature route (defaults to "featured")
     */
    protected $featureField;

    protected $perPage = 20;

    /*
     * Name of the index column to use as name column
     */
    protected $nameColumnKey = 'title';

    public function __construct(Application $app, Request $request)
    {
        $this->app = $app;
        $this->request = $request;

        $this->setMiddlewarePermission();

        $this->modelName = $this->getModelName();
        $this->routePrefix = $this->getRoutePrefix();
        $this->namespace = $this->getNamespace();
        $this->repository = $this->getRepository();
        $this->viewPrefix = $this->getViewPrefix();
        $this->modelTitle = $this->getModelTitle();
    }

    protected function setMiddlewarePermission()
    {
        $this->middleware('can:list', ['only' => ['index', 'show']]);
        $this->middleware('can:edit', ['only' => ['store', 'edit', 'update']]);
        $this->middleware('can:publish', ['only' => ['publish', 'feature', 'bulkPublish', 'bulkFeature']]);
        $this->middleware('can:reorder', ['only' => ['reorder']]);
        $this->middleware('can:delete', ['only' => ['destroy', 'bulkDelete', 'restore', 'bulkRestore']]);
    }

    protected function indexData($request)
    {
        return [];
    }

    protected function formData($request)
    {
        return [];
    }

    public function index()
    {
        $indexData = $this->getIndexData();

        if ($this->request->ajax()) {
            return $indexData + ['replaceUrl' => true];
        }

        $view = collect([
            "$this->viewPrefix.index",
            "cms-toolkit::$this->moduleName.index",
            "cms-toolkit::layouts.listing",
        ])->first(function ($view) {
            return view()->exists($view);
        });

        return view($view, $indexData);
    }

    public function getIndexData($prependScope = [])
    {
        $scopes = $this->filterScope($prependScope);
        $items = $this->getIndexItems($scopes);

        $data = [
            'moduleName' => $this->moduleName,
            'nameColumnKey' => $this->nameColumnKey,
            'tableData' => $this->getIndexTableData($items),
            'tableColumns' => $this->getIndexTableColumns($items),
            'tableMainFilters' => $this->getIndexTableMainFilters($items),
            'maxPage' => method_exists($items, 'lastPage') ? $items->lastPage() : 1,
            'defaultMaxPage' => method_exists($items, 'total') ? ceil($items->total() / $this->perPage) : 1,
            'offset' => method_exists($items, 'perPage') ? $items->perPage() : count($items),
            'defaultOffset' => $this->perPage,
            'reorder' => $this->getIndexOption('reorder'),
            'permalink' => $this->getIndexOption('permalink'),
            'filters' => json_decode($this->request->get('filter'), true) ?? [],
        ] + $this->getIndexUrls($this->moduleName, $this->routePrefix);

        return array_replace_recursive($data, $this->indexData($this->request));
    }

    public function getIndexItems($scopes = [], $forcePagination = false)
    {
        $perPage = request('offset') ?? $this->perPage ?? 50;
        return $this->repository->get($this->indexWith, $scopes, $this->orderScope(), $perPage, $forcePagination);
    }

    public function getIndexTableData($items)
    {
        return $items->map(function ($item) {
            $columnsData = collect($this->indexColumns)->mapWithKeys(function ($column) use ($item) {
                return $this->getItemColumnData($item, $column);
            })->toArray();

            $name = $columnsData[$this->nameColumnKey];
            unset($columnsData[$this->nameColumnKey]);

            $featuredField = $this->featureField ?? 'featured';

            return [
                'id' => $item->id,
                'name' => $name,
                'edit' => moduleRoute($this->moduleName, $this->routePrefix, 'edit', $item->id),
                'delete' => moduleRoute($this->moduleName, $this->routePrefix, 'destroy', $item->id),
            ] + $columnsData
                 + ($this->getIndexOption('publish') ? ['published' => $item->published] : [])
                 + ($this->getIndexOption('feature') ? ['featured' => $item->$featuredField] : [])
                 + (($this->getIndexOption('restore') && method_exists($item, 'trashed') && $item->trashed()) ? ['deleted' => true] : []);
        })->toArray();
    }

    public function getItemColumnData($item, $column)
    {
        if (isset($column['thumb']) && $column['thumb']) {
            $variant = isset($column['variant']);
            $role = $variant ? $column['variant']['role'] : head(array_keys($item->mediasParams));
            $crop = $variant ? $column['variant']['crop'] : head(array_keys(head($item->mediasParams)));
            $params = $variant && isset($column['variant']['params']) ? $column['variant']['params'] : ['w' => 80, 'h' => 80, 'fit' => 'crop'];

            return [
                'thumbnail' => $item->cmsImage($role, $crop, $params),
            ];
        }

        $field = $column['field'];
        $value = $item->$field;

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

    public function getIndexTableColumns($items)
    {
        $tableColumns = [];
        $visibleColumns = request('columns') ?? false;

        if (isset(array_first($this->indexColumns)['thumb']) && array_first($this->indexColumns)['thumb']) {
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
            'label' => $this->indexColumns[$this->nameColumnKey]['title'] ?? 'Name',
            'visible' => true,
            'optional' => false,
            'sortable' => true,
        ]);

        unset($this->indexColumns[$this->nameColumnKey]);

        foreach ($this->indexColumns as $column) {
            $columnName = isset($column['relationship']) ? $column['relationship'] . ucfirst($column['field']) : $column['field'];
            array_push($tableColumns, [
                'name' => $columnName,
                'label' => $column['title'],
                'visible' => $visibleColumns ? in_array($columnName, $visibleColumns) : ($column['visible'] ?? true),
                'optional' => $column['optional'] ?? true,
                'sortable' => $column['sort'] ?? false, // TODO: support a different sort field
            ]);
        }

        return $tableColumns;
    }

    public function getIndexTableMainFilters($items)
    {
        $statusFilters = [];

        array_push($statusFilters, [
            'name' => 'All items',
            'slug' => 'all',
            'number' => $this->repository->getCountByStatusSlug('all'),
        ]);

        if (method_exists($this->repository, 'beforeSaveHandleRevisions')) {
            array_push($statusFilters, [
                'name' => 'Mine',
                'slug' => 'mine',
                'number' => $this->repository->getCountByStatusSlug('mine'),
            ]);
        }

        array_push($statusFilters, [
            'name' => 'Published',
            'slug' => 'published',
            'number' => $this->repository->getCountByStatusSlug('published'),
        ], [
            'name' => 'Draft',
            'slug' => 'draft',
            'number' => $this->repository->getCountByStatusSlug('draft'),
        ]);

        if (!empty($items) && method_exists(array_first($items), 'trashed')) {
            array_push($statusFilters, [
                'name' => 'Trash',
                'slug' => 'trash',
                'number' => $this->repository->getCountByStatusSlug('trash'),
            ]);
        }

        return $statusFilters;
    }

    public function getIndexUrls($moduleName, $routePrefix)
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
            return [$endpoint . 'Url' => $this->getIndexOption($endpoint) ? moduleRoute($this->moduleName, $this->routePrefix, $endpoint) : null];
        })->toArray();
    }

    protected function getIndexOption($option)
    {
        $customOptionNamesMapping = [
            'store' => 'create',
        ];

        $option = array_key_exists($option, $customOptionNamesMapping) ? $customOptionNamesMapping[$option] : $option;
        return $this->indexOptions[$option] ?? $this->defaultIndexOptions[$option] ?? false;
    }

    public function browser()
    {
        return response()->json($this->getBrowserData());
    }

    public function getBrowserData($prependScope = [])
    {
        if (request()->has('except')) {
            $prependScope['exceptIds'] = request('except');
        }

        $scopes = $this->filterScope($prependScope);
        $items = $this->getBrowserItems($scopes);
        $data = $this->getBrowserTableData($items);

        return array_replace_recursive($data, $this->indexData($this->request));
    }

    public function getBrowserTableData($items)
    {
        return $items->map(function ($item) {
            $columnsData = collect($this->browserColumns)->mapWithKeys(function ($column) use ($item) {
                return $this->getItemColumnData($item, $column);
            })->toArray();

            $name = $columnsData[$this->nameColumnKey];
            unset($columnsData[$this->nameColumnKey]);

            return [
                'id' => $item->id,
                'name' => $name,
                'edit' => moduleRoute($this->moduleName, $this->routePrefix, 'edit', $item->id),
            ] + $columnsData;
        })->toArray();
    }

    public function getBrowserItems($scopes = [])
    {
        return $this->getIndexItems($scopes, true);
    }

    public function publish()
    {
        try {
            // TODO: validate publish is allowed based on model state
            $this->repository->updateBasic(request('id'), ['published' => !request('active')]);
        } catch (\Exception $e) {
            \Log::error($e);
            return $this->respondWithError($this->modelTitle . ' was not published. Something wrong happened!');
        }

        return $this->respondWithSuccess($this->modelTitle . ' ' . (request('active') ? 'un' : '') . 'published!');
    }

    public function bulkPublish()
    {
        try {
            // TODO: validate publish is allowed based on model state
            $this->repository->updateBasic(explode(',', request('ids')), ['published' => request('publish')]);
        } catch (\Exception $e) {
            \Log::error($e);
            return $this->respondWithError($this->modelTitle . ' items were not published. Something wrong happened!');
        }

        return $this->respondWithSuccess($this->modelTitle . ' items ' . (request('publish') ? '' : 'un') . 'published!');
    }

    public function destroy($id)
    {
        if ($this->repository->delete($id)) {
            return $this->respondWithSuccess($this->modelTitle . ' deleted!');
        }

        return $this->respondWithError($this->modelTitle . ' was not deleted. Something wrong happened!');
    }

    public function bulkDelete()
    {
        if ($this->repository->bulkDelete(explode(',', request('ids')))) {
            return $this->respondWithSuccess($this->modelTitle . ' items deleted!');
        }

        return $this->respondWithError($this->modelTitle . ' items were not deleted. Something wrong happened!');
    }

    public function restore()
    {
        if ($this->repository->restore(request('id'))) {
            return $this->respondWithSuccess($this->modelTitle . ' restored!');
        }

        return $this->respondWithError($this->modelTitle . ' was not restored. Something wrong happened!');
    }

    public function bulkRestore()
    {
        if ($this->repository->bulkRestore(explode(',', request('ids')))) {
            return $this->respondWithSuccess($this->modelTitle . ' items restored!');
        }

        return $this->respondWithError($this->modelTitle . ' items were not restored. Something wrong happened!');
    }

    public function feature()
    {
        if (($id = request('id'))) {
            $featuredField = request('featureField') ?? ($this->featureField ?? 'featured');
            $featured = !request('active');

            if ($this->repository->isUniqueFeature()) {
                if ($featured) {
                    $this->repository->updateBasic(null, [$featuredField => false]);
                    $this->repository->updateBasic($id, [$featuredField => $featured]);
                }
            } else {
                $this->repository->updateBasic($id, [$featuredField => $featured]);
            }

            return $this->respondWithSuccess($this->modelTitle . ' ' . (request('active') ? 'un' : '') . 'featured!');
        }

        return $this->respondWithError($this->modelTitle . ' was not featured. Something wrong happened!');
    }

    public function bulkFeature()
    {
        if (($ids = explode(',', request('ids')))) {
            $featuredField = request('featureField') ?? ($this->featureField ?? 'featured');
            $featured = request('feature') ?? true;
            // we don't need to check if unique feature since bulk operation shouldn't be allowed in this case
            $this->repository->updateBasic($ids, [$featuredField => $featured]);

            return $this->respondWithSuccess($this->modelTitle . ' items ' . (request('feature') ? '' : 'un') . 'featured!');
        }

        return $this->respondWithError($this->modelTitle . ' items were not featured. Something wrong happened!');
    }

    public function reorder()
    {
        if (($values = request('ids')) && !empty($values)) {
            $this->repository->setNewOrder($values);
            return $this->respondWithSuccess(camelCaseToWords($this->modelTitle) . ' order changed!');
        }

        return $this->respondWithError($this->modelTitle . ' order was not changed. Something wrong happened!');
    }

    public function store()
    {
        $input = $this->request->all();
        if (isset($input['cancel'])) {
            return redirect($this->getBackLink(null));
        }

        $formRequest = $this->validateFormRequest();
        $item = $this->repository->create($formRequest->all());
        return $this->redirectToForm($item->id);
    }

    public function show($id)
    {
        return $this->redirectToForm($id);
    }

    public function edit($id)
    {
        $this->setBackLink();
        $this->addLock($id);

        $view = collect([
            "$this->viewPrefix.form",
            "cms-toolkit::$this->moduleName.form",
            "cms-toolkit::layouts.form",
        ])->first(function ($view) {
            return view()->exists($view);
        });

        return view($view, $this->form($id));
    }

    protected function form($id)
    {
        $item = $this->repository->getById($id, $this->formWith, $this->formWithCount);

        $fullRoutePrefix = 'admin.' . ($this->routePrefix ? $this->routePrefix . '.' : '') . $this->moduleName . '.';
        $previewRouteName = $fullRoutePrefix . 'preview';
        $restoreRouteName = $fullRoutePrefix . 'restore';

        $data = [
            'item' => $item,
            'revisions' => method_exists($item, 'hasRevisions') && $item->hasRevisions() ? $item->revisionsForPublisher() : [],
            'form_fields' => $this->repository->getFormFields($item),
            'saveUrl' => moduleRoute($this->moduleName, $this->routePrefix, 'update', $id),
            'back_link' => $this->getBackLink(),
            'moduleName' => $this->moduleName,
            'modelName' => $this->modelName,
            'routePrefix' => $this->routePrefix,
            'baseUrl' => $item->urlWithoutSlug ?? config('app.url') . '/',
        ] + (Route::has($previewRouteName) ? [
            'previewUrl' => moduleRoute($this->moduleName, $this->routePrefix, 'preview', $id),
        ] : []) + (Route::has($restoreRouteName) ? [
            'restoreUrl' => moduleRoute($this->moduleName, $this->routePrefix, 'restoreRevision', $id),
        ] : []);

        return array_replace_recursive($data, $this->formData($this->request));
    }

    public function update($id)
    {
        $item = $this->repository->getById($id);
        $input = $this->request->all();
        if (isset($input['cancel'])) {
            if ($item->isLockable() && $item->isLocked() && $item->isLockedByCurrentUser()) {
                $this->removeLock($id);
            }
            return redirect($this->getBackLink(null));
        } else {

            $formRequest = $this->validateFormRequest();

            if (($item->isLockable() == false) || ($item->isLocked() && $item->isLockedByCurrentUser())) {
                // check the lock?
                $this->repository->update($id, $formRequest->all());
                return $this->redirectToForm($id);
            } else {
                abort(403);
            }
        }
    }

    public function preview($id)
    {
        $formRequest = $this->request;

        $comparing = $formRequest->has('_compare') && $formRequest->input('_compare');
        $revision = $formRequest->has('_revision') && $formRequest->input('_revision');

        // trigger FormRequest validation unless previewing a revision
        if ($comparing || !$revision) {
            $formRequest = $this->validateFormRequest();
        }

        if ($revision) {
            $object = $this->repository->previewForRevision($id, $formRequest->input('_revision'));
        } elseif ($comparing) {
            $object = $this->repository->previewForCompare($id);
        } else {
            $object = $this->repository->preview($id, $formRequest->all());
        }

        if ($comparing) {
            $objectToCompare = $this->repository->preview($id, $formRequest->all());
            session()->flash('_preview_' . $this->moduleName . '_' . $id, $objectToCompare);
            session()->flash('_compare_' . $this->moduleName . '_' . $id, $object);
        } else {
            session()->flash('_preview_' . $this->moduleName . '_' . $id, $object);
        }

        return response()->json('ok', 200);
    }

    public function status($id)
    {
        $item = $this->repository->getById($id);

        $response_data = [
            'status' => 'ok',
        ];

        // include other information, revisions count, etc.
        $lockStatus = ['locked' => false];
        if ($item->isLockable()) {
            if ($item->isLocked()) {
                $lockStatus['locked'] = true;
                $lockStatus['locked_by']['id'] = $item->lockedBy()->id;
                $lockStatus['locked_by']['name'] = $item->lockedBy()->name;
            }
        }
        $response_data['lock'] = $lockStatus;

        return response()->json($response_data, 200);
    }

    protected function validateFormRequest()
    {
        return $this->app->make("$this->namespace\Http\Requests\Admin\\" . $this->modelName . "Request");
    }

    private function respondWithSuccess($message)
    {
        return $this->respondWithJson($message, FlashLevel::SUCCESS);
    }

    private function respondWithError($message)
    {
        return $this->respondWithJson($message, FlashLevel::ERROR);
    }

    private function respondWithJson($message, $variant)
    {
        return response()->json([
            'message' => $message,
            'variant' => $variant,
        ]);
    }

    public function tags()
    {
        $query = $this->request->input('q');
        $tags = $this->repository->getTags($query);

        return response()->json(['items' => $tags->map(function ($tag) {
            return $tag->name;
        })], 200);
    }

    protected function orderScope()
    {
        $orders = [];
        if ($this->request->has("sortKey") && $this->request->has("sortDir")) {
            if (($key = $this->request->get("sortKey")) == 'name') {
                $orders[$this->nameColumnKey] = $this->request->get("sortDir");
            } elseif (!empty($key)) {
                $orders[$key] = $this->request->get("sortDir");
            }
        }

        // don't apply default orders if reorder is enabled
        $reorder = $this->getIndexOption('reorder');
        $defaultOrders = ($reorder ? [] : ($this->defaultOrders ?? []));

        return $orders + $defaultOrders;
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
        return json_decode($this->request->get('filter'), true) ?? [];
    }

    protected function addLock($id)
    {
        $item = $this->repository->getById($id);

        if ($item->isLockable()) {
            if (!$item->isLocked()) {
                $item->lock(null, Auth::user());
                return true;
            } else {
                // was this lock held by the current user?
                if ($item->lockedBy()->id == Auth::user()->id) {
                    return true;
                }
            }
        }

        return false;
    }

    protected function removeLock($id, $forceUnlock = false)
    {
        $item = $this->repository->getById($id);

        if ($item->isLockable()) {
            if ($forceUnlock || ($item->lockedBy()->id == Auth::user()->id)) {
                $item->unlock();
                return true;
            }
        }

        return false;
    }

    protected function setBackLink($back_link = null, $params = [])
    {
        if (!isset($back_link)) {
            if (($back_link = Session::get($this->moduleName . "_back_link")) == null) {
                $back_link = $this->request->headers->get('referer') ?? moduleRoute($this->moduleName, $this->routePrefix, "index", $params);
            }
        }

        if (!$this->request->has('retain')) {
            Session::put($this->moduleName . "_back_link", $back_link);
        }
    }

    protected function getBackLink($fallback = null, $params = [])
    {
        $back_link = Session::get($this->moduleName . "_back_link", $fallback);
        return $back_link ?? moduleRoute($this->moduleName, $this->routePrefix, "index", $params);
    }

    protected function redirectToForm($id = null, $params = [])
    {
        $input = $this->request->all();
        if (isset($input['finish'])) {
            flash()->message('All good!', FlashLevel::SUCCESS);
            return redirect($this->getBackLink(null, $params));
        } elseif ($id == null) {
            return redirect(moduleRoute($this->moduleName, $this->routePrefix, "create", $params + ['retain' => true]));
        } else {
            flash()->message('All good!', FlashLevel::SUCCESS);
            return redirect(moduleRoute($this->moduleName, $this->routePrefix, "edit", $params + ['id' => $id, 'retain' => true]));
        }
    }

    protected function getNamespace()
    {
        return $this->namespace ?? config('cms-toolkit.namespace');
    }

    protected function getRoutePrefix()
    {
        if ($this->request->route() != null) {
            $routePrefix = ltrim($this->request->route()->getPrefix(), "/");
            return str_replace("/", ".", ($routePrefix));
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
}
