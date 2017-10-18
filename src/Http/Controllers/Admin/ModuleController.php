<?php

namespace A17\CmsToolkit\Http\Controllers\Admin;

use A17\CmsToolkit\Helpers\FlashLevel;
use A17\CmsToolkit\Repositories\FileRepository;
use A17\CmsToolkit\Repositories\MediaRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Auth;
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
            'title' => (count($items) > 1 ? $this->moduleName : str_singular($this->moduleName)),
            'moduleName' => $this->moduleName,
            'modelName' => $this->modelName,
            'routePrefix' => $this->routePrefix,
            'filters' => array_keys(array_except($this->filters, array_keys($this->defaultFilters))),
            'filtersOn' => !empty(array_except($scopes, array_keys($prependScope))),
        ];

        return array_replace_recursive($data, $this->indexData($this->request));
    }

    public function create()
    {
        $this->setBackLink();

        $data = [
            'form_options' => [
                'method' => 'POST',
                'url' => moduleRoute($this->moduleName, $this->routePrefix, 'store'),
            ] + $this->defaultFormOptions(),
            'form_fields' => $this->repository->getOldFormFieldsOnCreate(),
            'back_link' => $this->getBackLink(),
            'moduleName' => $this->moduleName,
            'modelName' => $this->modelName,
            'routePrefix' => $this->routePrefix,
        ];
        $view = view()->exists("$this->viewPrefix.form") ? "$this->viewPrefix.form" : "cms-toolkit::{$this->moduleName}.form";
        return view($view, array_replace_recursive($data, $this->formData($this->request)));
    }

    public function repeater()
    {
        $data = [
            'form_fields' => $this->repository->getOldFormFieldsOnCreate(),
            'repeaterIndex' => request('index'),
            'moduleName' => $this->moduleName,
            'modelName' => $this->modelName,
            'repeater' => true,
            'routePrefix' => $this->routePrefix,
        ];

        return view("$this->viewPrefix.repeater", array_replace_recursive($data, $this->formData($this->request)));
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
        if ($this->request->ajax() && $this->request->has('rev_page')) {
            return $this->revisions($id);
        }

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

    private function revisions($id)
    {
        $view = view()->exists("$this->viewPrefix._versions_lines") ? "$this->viewPrefix._versions_lines" : "cms-toolkit::layouts.form_partials._versions_lines";
        return view($view, ['with_preview' => $this->withPreview ?? true] + $this->form($id));
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
            'status' => 'ok'
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
        if (($id = $this->request->input('id')) && ($active = $this->request->input('active'))) {
            $this->repository->updateBasic($id, ['published' => $active == 'true' ? 0 : 1]);
            return response("Done!");
        }
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

    public function media()
    {
        $mediaModels = [];
        foreach ($this->request->input('data') as $media) {
            $mediaModels[$media['id']] = app(MediaRepository::class)->getById($media['id']);
        }

        $role = ($this->request->input('backend_role') ?? $this->request->input('role'));

        $crops = $this->repository->getCrops($role);

        $view = view()->exists('admin.medias.insert_template') ? 'admin.medias.insert_template' : 'cms-toolkit::medias.insert_template';

        return view($view)->with([
            'images' => $mediaModels,
            'crops' => $crops,
            'backend_role' => $role,
            'media_role' => $this->request->input('role'),
            'new_row' => true,
            'with_crop' => $this->request->input('with_crop'),
            'with_multiple' => $this->request->input('with_multiple'),
            'with_background_position' => $this->request->input('with_background_position'),
            'repeater' => $this->request->input('repeater'),
            'repeaterIndex' => $this->request->input('repeater_index'),
            'moduleName' => $this->moduleName,
        ]);
    }

    public function file()
    {
        $fileModels = [];
        foreach ($this->request->input('data') as $file) {
            $fileModels[$file['id']] = app(FileRepository::class)->getById($file['id']);
        }

        $view = view()->exists('admin.files.insert_template') ? 'admin.files.insert_template' : 'cms-toolkit::files.insert_template';

        return view($view)->with([
            'files' => $fileModels,
            'file_role' => $this->request->input('file_role'),
            'new_row' => true,
            'with_multiple' => $this->request->input('with_multiple'),
            'locale' => $this->request->input('locale'),
            'repeater' => $this->request->input('repeater'),
            'repeaterIndex' => $this->request->input('repeater_index'),
            'moduleName' => $this->moduleName,
        ]);
    }

    public function browser()
    {
        if (!is_null($this->request->input('page'))) {
            $view = view()->exists('admin.' . $this->moduleName . '._browser_list')
            ? 'admin.' . $this->moduleName . '._browser_list'
            : (view()->exists('admin.layouts.resources._browser_list')
                ? 'admin.layouts.resources._browser_list'
                : 'cms-toolkit::layouts.resources._browser_list');

            return view($view, $this->getBrowserData() + $this->request->all());
        }

        $view = view()->exists('admin.' . $this->moduleName . '.browser')
        ? 'admin.' . $this->moduleName . '.browser'
        : (view()->exists('admin.layouts.resources.browser')
            ? 'admin.layouts.resources.browser'
            : 'cms-toolkit::layouts.resources.browser');

        return view($view, $this->getBrowserData() + $this->request->all());
    }

    public function insert()
    {
        $elements = [];
        foreach ($this->request->input('data') as $element) {
            $elements[$element['id']] = $this->repository->getById($element['id']);
        }

        $view = view()->exists('admin.' . $this->moduleName . '._browser_insert')
        ? 'admin.' . $this->moduleName . '._browser_insert'
        : (view()->exists('admin.layouts.resources._browser_insert')
            ? 'admin.layouts.resources._browser_insert'
            : 'cms-toolkit::layouts.resources._browser_insert');

        return view($view)->withItems($elements)
            ->withElementRole($this->request->input('role'))
            ->withNewRow(true)
            ->withWithMultiple($this->request->input('with_multiple'))
            ->withWithSort($this->request->input('with_sort'));
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
        if ($this->request->has("sortField") && $this->request->has("sortOrder")) {
            $orders[$this->request->get("sortField")] = $this->request->get("sortOrder");
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

    protected function removeLock($id, $forceUnlock=false)
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
