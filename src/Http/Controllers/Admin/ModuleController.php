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

    protected $modelName;

    protected $repository;

    protected $routePrefix;

    protected $defaultFilters = [
        'fSearch' => 'title|search',
    ];

    /*
     * Define this in your controller implementation
     */
    protected $moduleName;

    /*
     * Available columns of the index view
     */
    protected $indexColumns = [];

    /*
     * Options of the index view
     */
    protected $indexOptions = [];

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
     * Filters mapping ('fFilterName' => 'filterColumn')
     * In the indexData function, name your lists with the filter name + List (fClientList for example)
     */
    protected $filters = [];

    /*
     * Feature field name if the controller is using the feature route (defaults to "featured")
     */
    protected $featureField;

    protected $perPage = 50;

    protected $breadcrumb = false;

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
        if ($this->request->ajax()) {
            return $this->getIndexData() + $this->request->all();
        }

        $view = view()->exists("$this->viewPrefix.index") ? "$this->viewPrefix.index" : "cms-toolkit::{$this->moduleName}.index";
        return view($view, $this->getIndexData() + $this->request->all());
    }

    public function getIndexData($prependScope = [])
    {
        $scopes = $this->filterScope($prependScope);
        return $this->getViewData($this->getIndexItems($scopes), $scopes, $prependScope);
    }

    public function getIndexItems($scopes = [], $forcePagination = false)
    {
        return $this->repository->get($this->indexWith, $scopes, $this->orderScope(), $this->perPage ?? 50, $forcePagination);
    }

    public function getViewData($items, $scopes, $prependScope = [])
    {
        $data = [
            'items' => $items,
            'filters' => array_keys(array_except($this->filters, array_keys($this->defaultFilters))),
            'filtersOn' => !empty(array_except($scopes, array_keys($prependScope))),
            'mappedData' => $this->getIndexMappedData($items),
            'mappedColumns' => $this->getIndexMappedColumns($items),
            'maxPage' => $items->lastPage(),
            'offset' => $items->perPage(),
            'nameColumnKey' => $this->nameColumnKey,
            'publishUrl' => moduleRoute($this->moduleName, $this->routePrefix, 'publish'),
        ];

        return array_replace_recursive($data, $this->indexData($this->request));
    }

    public function getIndexMappedData($items)
    {
        return $items->map(function ($item) {
            $columnsData = collect($this->indexColumns)->mapWithKeys(function ($column, $key) use ($item) {
                if (isset($column['thumb']) && $column['thumb']) {
                    return [
                        'thumbnail' => $item->cmsImage(isset($column['variant']) ? $column['variant']['role'] : head(array_keys($item->mediasParams)), isset($column['variant']) ? $column['variant']['crop'] : head(array_keys(head($item->mediasParams))), isset($column['variant']) && isset($column['variant']['params']) ? $column['variant']['params'] : ['w' => 80, 'h' => 80, 'fit' => 'crop']),
                    ];
                }
                $field = $column['field'];
                return [
                    "$field" => $item->$field,
                ];
            })->toArray();

            $name = $columnsData[$this->nameColumnKey];
            unset($columnsData[$this->nameColumnKey]);

            return [
                'id' => $item->id,
                'name' => $name,
                'edit' => moduleRoute($this->moduleName, $this->routePrefix, 'edit', $item->id),
            ] + $columnsData
                 + (($this->indexOptions['publish'] ?? true) ? ['published' => $item->published] : [])
                 + (($this->indexOptions['feature'] ?? false) ? ['featured' => $item->featured] : []);
        })->toArray();
    }

    public function getIndexMappedColumns($items)
    {
        $mappedColumns = [];
        if (isset(array_first($this->indexColumns)['thumb']) && array_first($this->indexColumns)['thumb']) {
            array_push($mappedColumns, [
                'name' => 'thumbnail',
                'label' => 'Thumbnail',
                'visible' => true,
                'optional' => true,
                'sortable' => false,
            ]);
            array_shift($this->indexColumns);
        }

        if ($this->indexOptions['feature'] ?? false) {
            array_push($mappedColumns, [
                'name' => 'featured',
                'label' => 'Featured',
                'visible' => true,
                'optional' => false,
                'sortable' => false,
            ]);
        }

        if ($this->indexOptions['published'] ?? true) {
            array_push($mappedColumns, [
                'name' => 'published',
                'label' => 'Published',
                'visible' => true,
                'optional' => false,
                'sortable' => false,
            ]);
        }

        array_push($mappedColumns, [
            'name' => 'name',
            'label' => 'Name',
            'visible' => true,
            'optional' => false,
            'sortable' => true,
        ]);

        unset($this->indexColumns[$this->nameColumnKey]);

        foreach ($this->indexColumns as $column) {
            array_push($mappedColumns, [
                'name' => $column['field'],
                'label' => $column['title'],
                'visible' => $column['optional'] ?? true,
                'optional' => $column['optional'] ?? true,
                'sortable' => $column['sort'] ?? false,
            ]);
        }

        return $mappedColumns;
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

    // TODO revisions paginated endpoint

    public function edit($id)
    {
        $this->setBackLink();
        $this->addLock($id);
        $view = view()->exists("$this->viewPrefix.form") ? "$this->viewPrefix.form" : "cms-toolkit::{$this->moduleName}.form";
        return view($view, $this->form($id));
    }

    protected function form($id)
    {
        $item = $this->repository->getById($id, $this->formWith, $this->formWithCount);

        $fullRoutePrefix = 'admin.' . ($this->routePrefix ? $this->routePrefix . '.' : '') . $this->moduleName . '.';
        $previewRouteName = $fullRoutePrefix . 'preview';
        $restoreRouteName = $fullRoutePrefix . 'restore';

        $data = [
            'form_options' => [
                'method' => 'PUT',
                'url' => moduleRoute($this->moduleName, $this->routePrefix, 'update', $id),
            ] + (Route::has($previewRouteName) ? [
                'data-preview-url' => moduleRoute($this->moduleName, $this->routePrefix, 'preview', $id),
            ] : []) + (Route::has($restoreRouteName) ? [
                'data-restore-url' => moduleRoute($this->moduleName, $this->routePrefix, 'restore', $id),
            ] : []) + $this->defaultFormOptions(),
            'item' => $item,
            'form_fields' => $this->repository->getFormFields($item),
            'back_link' => $this->getBackLink(),
            'moduleName' => $this->moduleName,
            'modelName' => $this->modelName,
            'routePrefix' => $this->routePrefix,
            'breadcrumb' => $this->getBreadcrumb($id),
        ];

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

    public function destroy($id)
    {
        if (($id = $this->request->input('id'))) {
            $this->repository->delete($id);
            flash()->message($this->modelName . ' deleted!', FlashLevel::SUCCESS);
        } else {
            flash()->message($this->modelName . ' was not deleted. Something wrong happened!', FlashLevel::ERROR);
        }
    }

    public function publish()
    {
        $this->repository->updateBasic(request('id'), ['published' => !request('active')]);
        return response("Done!");
    }

    public function feature()
    {
        if (($id = $this->request->input('id')) && ($previousActiveState = $this->request->input('active'))) {
            $featured = ($previousActiveState == 'true' ? false : true);
            $featuredField = $this->request->input('featureField') ?? ($this->featureField ?? 'featured');

            if ($this->repository->isUniqueFeature()) {
                if ($featured) {
                    $this->repository->updateBasic(null, [$featuredField => false]);
                    $this->repository->updateBasic($id, [$featuredField => $featured]);
                }
            } else {
                $this->repository->updateBasic($id, [$featuredField => $featured]);
            }

            return response($featured ? "Item featured!" : "Item unfeatured!");
        }
    }

    public function sort()
    {
        if (($values = $this->request->getContent()) && !empty($values)) {
            $this->repository->setNewOrder(explode(',', $values));
        }
    }

    public function tags()
    {
        $query = $this->request->input('query');
        $tags = $this->repository->getTags($query);
        return response()->json($tags, 200);
    }

    public function browser()
    {
        return $this->getBrowserData() + $this->request->all();
    }

    public function getBrowserData($prependScope = [])
    {
        $scopes = $this->filterScope($prependScope);
        return $this->getViewData($this->getBrowserItems($scopes), $scopes, $prependScope);
    }

    public function getBrowserItems($scopes = [])
    {
        return $this->getIndexItems($scopes, true);
    }

    protected function orderScope()
    {
        $orders = [];
        if ($this->request->has("sortKey") && $this->request->has("sortDir")) {
            if (($key = $this->request->has("sortKey")) == 'name') {
                $orders[$this->nameColumnKey] = $this->request->get("sortDir");
            } else {
                $orders[$key] = $this->request->get("sortDir");
            }
        }
        return $orders + ($this->defaultOrders ?? []);
    }

    protected function filterScope($prepend = [])
    {
        $scope = [];

        $this->filters = array_merge($this->filters, $this->defaultFilters);

        foreach ($this->filters as $key => $field) {
            if ($this->request->has($key)) {
                $value = $this->request->$key;
                if ($value == 0 || !empty($value)) {
                    // add some syntaxic sugar to scope the same filter on multiple columns
                    $fieldSplitted = explode('|', $field);
                    if (count($fieldSplitted) > 1) {
                        $requestValue = $this->request->$key;
                        collect($fieldSplitted)->each(function ($scopeKey) use (&$scope, $requestValue) {
                            $scope[$scopeKey] = $requestValue;
                        });
                    } else {
                        $scope[$field] = $this->request->$key;
                    }
                }
            }
        }

        return $prepend + $scope;
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

    protected function defaultFormOptions()
    {
        return [
            'id' => $this->moduleName . '_form',
            'class' => "simple_form",
            'accept-charset' => "UTF-8",
            'novalidate' => "novalidate",
        ] + (app()->isLocal() ? [] : [
            'data-behavior' => 'navigate_away',
            'data-navigate-away-confirm' => 'Any changes will be lost.',
        ]);
    }

    protected function getBreadcrumb($item_id, $append = [])
    {
        if (isset($this->breadcrumb) ? $this->breadcrumb : true) {
            $breadcrumb = [];

            if (!$item_id) {
                return $breadcrumb;
            }

            $breadcrumb[ucfirst($this->moduleName)] = moduleRoute($this->moduleName, $this->routePrefix, "index");

            if (($obj = $this->repository->getById($item_id)) !== null && isset($obj->title)) {
                $breadcrumb[$obj->title] = null;
            }

            return $breadcrumb + $append;
        }
    }

    protected function getNamespace()
    {
        return $this->namespace ?? config('cms-toolkit.namespace');
    }

    protected function getRoutePrefix()
    {
        $routePrefix = ($this->request->route() != null) ? ltrim($this->request->route()->getPrefix(), "/") : '';
        return str_replace("/", ".", ($routePrefix));
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

    protected function setMiddlewarePermission()
    {
        $this->middleware('can:list', ['only' => ['index', 'show']]);
        $this->middleware('can:edit', ['only' => ['create', 'store', 'edit', 'update', 'media', 'file']]);
        $this->middleware('can:publish', ['only' => ['publish', 'feature']]);
        $this->middleware('can:sort', ['only' => ['sort']]);
        $this->middleware('can:delete', ['only' => ['destroy']]);
    }
}
