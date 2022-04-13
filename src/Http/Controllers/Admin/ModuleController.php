<?php

namespace A17\Twill\Http\Controllers\Admin;

use A17\Twill\Exceptions\NoCapsuleFoundException;
use A17\Twill\Facades\TwillBlocks;
use A17\Twill\Facades\TwillCapsules;
use A17\Twill\Helpers\FlashLevel;
use A17\Twill\Models\Behaviors\HasSlug;
use A17\Twill\Services\Blocks\Block;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
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

    /**
     * @var \A17\Twill\Repositories\ModuleRepository
     */
    protected $repository;

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
        // @todo(3.x): Default to true.
        'includeScheduledInList' => false,
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
     */
    protected $filters = [];

    /**
     * Additional links to display in the listing filter.
     *
     * @var array
     */
    protected $filterLinks = [];

    /**
     * Filters that are selected by default in the index view.
     *
     * Example: 'filter_key' => 'default_filter_value'
     *
     * @var array
     */
    protected $filtersDefaultOptions = [];

    /**
     * Default orders for the index view.
     *
     * @var array
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
     */
    protected $indexColumns;

    /**
     * @var array
     */
    protected $browserColumns;

    /**
     * @var string
     */
    protected $permalinkBase;

    /**
     * @var array
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
        if (! isset($this->defaultFilters)) {
            $this->defaultFilters = [
                'search' => ($this->moduleHas('translations') ? '' : '%') . $this->titleColumnKey,
            ];
        }

        /*
         * Apply any filters that are selected by default
         */
        $this->applyFiltersDefaultOptions();

        /*
         * Available columns of the index view
         */
        if (! isset($this->indexColumns)) {
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
        if (! isset($this->browserColumns)) {
            $this->browserColumns = [
                $this->titleColumnKey => [
                    'title' => ucfirst($this->titleColumnKey),
                    'field' => $this->titleColumnKey,
                ],
            ];
        }
    }

    /**
     * @return void
     */
    protected function setMiddlewarePermission()
    {
        $this->middleware('can:list', ['only' => ['index', 'show']]);
        $this->middleware('can:edit', ['only' => ['store', 'edit', 'update']]);
        $this->middleware('can:duplicate', ['only' => ['duplicate']]);
        $this->middleware('can:publish', ['only' => ['publish', 'feature', 'bulkPublish', 'bulkFeature']]);
        $this->middleware('can:reorder', ['only' => ['reorder']]);
        $this->middleware('can:delete', ['only' => ['destroy', 'bulkDelete', 'restore', 'bulkRestore', 'forceDelete', 'bulkForceDelete', 'restoreRevision']]);
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
     * @param int|null $parentModuleId
     * @return array|\Illuminate\View\View
     */
    public function index($parentModuleId = null)
    {
        $parentModuleId = $this->getParentModuleIdFromRequest($this->request) ?? $parentModuleId;

        $this->submodule = isset($parentModuleId);
        $this->submoduleParentId = $parentModuleId;

        $indexData = $this->getIndexData($this->submodule ? [
            $this->getParentModuleForeignKey() => $this->submoduleParentId,
        ] : []);

        if ($this->request->ajax()) {
            return $indexData + ['replaceUrl' => true];
        }

        if ($this->request->has('openCreate') && $this->request->get('openCreate')) {
            $indexData += ['openCreate' => true];
        }

        $view = Collection::make([
            "$this->viewPrefix.index",
            "twill::$this->moduleName.index",
            'twill::layouts.listing',
        ])->first(function ($view) {
            return View::exists($view);
        });

        return View::make($view, $indexData);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function browser()
    {
        return Response::json($this->getBrowserData());
    }

    /**
     * @param int|null $parentModuleId
     * @return \Illuminate\Http\JsonResponse
     */
    public function store($parentModuleId = null)
    {
        $parentModuleId = $this->getParentModuleIdFromRequest($this->request) ?? $parentModuleId;

        $input = $this->validateFormRequest()->all();
        $optionalParent = $parentModuleId ? [$this->getParentModuleForeignKey() => $parentModuleId] : [];

        if (isset($input['cmsSaveType']) && $input['cmsSaveType'] === 'cancel') {
            return $this->respondWithRedirect(moduleRoute(
                $this->moduleName,
                $this->routePrefix,
                'create'
            ));
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
            return $this->respondWithRedirect(moduleRoute(
                $this->moduleName,
                $this->routePrefix,
                'create'
            ));
        }

        return $this->respondWithRedirect(moduleRoute(
            $this->moduleName,
            $this->routePrefix,
            'edit',
            [Str::singular(last(explode('.', $this->moduleName))) => $this->getItemIdentifier($item)]
        ));
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
     * @param int $id
     * @param int|null $submoduleId
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($id, $submoduleId = null)
    {
        $params = $this->request->route()->parameters();

        $this->submodule = count($params) > 1;
        $this->submoduleParentId = $this->submodule
        ? $this->getParentModuleIdFromRequest($this->request) ?? $id
        : head($params);

        $id = last($params);

        if ($this->getIndexOption('editInModal')) {
            return $this->request->ajax()
            ? Response::json($this->modalFormData($id))
            : Redirect::to(moduleRoute($this->moduleName, $this->routePrefix, 'index'));
        }

        $this->setBackLink();

        $view = Collection::make([
            "$this->viewPrefix.form",
            "twill::$this->moduleName.form",
            'twill::layouts.form',
        ])->first(function ($view) {
            return View::exists($view);
        });

        return View::make($view, $this->form($id));
    }

    /**
     * @param int $parentModuleId
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function create($parentModuleId = null)
    {
        if (! $this->getIndexOption('skipCreateModal')) {
            return Redirect::to(moduleRoute(
                $this->moduleName,
                $this->routePrefix,
                'index',
                ['openCreate' => true]
            ));
        }

        $parentModuleId = $this->getParentModuleIdFromRequest($this->request) ?? $parentModuleId;

        $this->submodule = isset($parentModuleId);
        $this->submoduleParentId = $parentModuleId;

        $view = Collection::make([
            "$this->viewPrefix.form",
            "twill::$this->moduleName.form",
            'twill::layouts.form',
        ])->first(function ($view) {
            return View::exists($view);
        });

        return View::make($view, $this->form(null));
    }

    /**
     * @param int $id
     * @param int|null $submoduleId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id, $submoduleId = null)
    {
        $params = $this->request->route()->parameters();

        $submoduleParentId = $this->getParentModuleIdFromRequest($this->request) ?? $id;
        $this->submodule = isset($submoduleParentId);
        $this->submoduleParentId = $submoduleParentId;

        $id = last($params);

        $item = $this->repository->getById($id);
        $input = $this->request->all();

        if (isset($input['cmsSaveType']) && $input['cmsSaveType'] === 'cancel') {
            return $this->respondWithRedirect(moduleRoute(
                $this->moduleName,
                $this->routePrefix,
                'edit',
                [Str::singular($this->moduleName) => $id]
            ));
        } else {
            $formRequest = $this->validateFormRequest();

            $this->repository->update($id, $formRequest->all());

            activity()->performedOn($item)->log('updated');

            $this->fireEvent();

            if (isset($input['cmsSaveType'])) {
                if (Str::endsWith($input['cmsSaveType'], '-close')) {
                    return $this->respondWithRedirect($this->getBackLink());
                } elseif (Str::endsWith($input['cmsSaveType'], '-new')) {
                    if ($this->getIndexOption('skipCreateModal')) {
                        return $this->respondWithRedirect(moduleRoute(
                            $this->moduleName,
                            $this->routePrefix,
                            'create'
                        ));
                    }

                    return $this->respondWithRedirect(moduleRoute(
                        $this->moduleName,
                        $this->routePrefix,
                        'index',
                        ['openCreate' => true]
                    ));
                } elseif ($input['cmsSaveType'] === 'restore') {
                    Session::flash('status', twillTrans('twill::lang.publisher.restore-success'));

                    return $this->respondWithRedirect(moduleRoute(
                        $this->moduleName,
                        $this->routePrefix,
                        'edit',
                        [Str::singular($this->moduleName) => $id]
                    ));
                }
            }

            if ($this->moduleHas('revisions')) {
                return Response::json([
                    'message' => twillTrans('twill::lang.publisher.save-success'),
                    'variant' => FlashLevel::SUCCESS,
                    'revisions' => $item->revisionsArray(),
                ]);
            }

            return $this->respondWithSuccess(twillTrans('twill::lang.publisher.save-success'));
        }
    }

    /**
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function preview($id)
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

        $previewView = $this->previewView ?? (Config::get('twill.frontend.views_path', 'site') . '.' . Str::singular($this->moduleName));

        return View::exists($previewView) ? View::make($previewView, array_replace([
            'item' => $item,
        ], $this->previewData($item))) : View::make('twill::errors.preview', [
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

        $view = Collection::make([
            "$this->viewPrefix.form",
            "twill::$this->moduleName.form",
            'twill::layouts.form',
        ])->first(function ($view) {
            return View::exists($view);
        });

        $revision = $item->revisions()->where('id', $this->request->get('revisionId'))->first();
        $date = $revision->created_at->toDayDateTimeString();

        Session::flash('restoreMessage', twillTrans('twill::lang.publisher.restore-message', ['user' => $revision->byUser, 'date' => $date]));

        return View::make($view, $this->form($id, $item));
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function publish()
    {
        try {
            if ($this->repository->updateBasic($this->request->get('id'), [
                'published' => ! $this->request->get('active'),
            ])) {
                activity()->performedOn(
                    $this->repository->getById($this->request->get('id'))
                )->log(
                    ($this->request->get('active') ? 'un' : '') . 'published'
                );

                $this->fireEvent();

                if ($this->request->get('active')) {
                    return $this->respondWithSuccess(twillTrans('twill::lang.listing.publish.unpublished', ['modelTitle' => $this->modelTitle]));
                } else {
                    return $this->respondWithSuccess(twillTrans('twill::lang.listing.publish.published', ['modelTitle' => $this->modelTitle]));
                }
            }
        } catch (\Exception $e) {
            \Log::error($e);
        }

        return $this->respondWithError(twillTrans('twill::lang.listing.publish.error', ['modelTitle' => $this->modelTitle]));
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkPublish()
    {
        try {
            if ($this->repository->updateBasic(explode(',', $this->request->get('ids')), [
                'published' => $this->request->get('publish'),
            ])) {
                $this->fireEvent();
                if ($this->request->get('publish')) {
                    return $this->respondWithSuccess(twillTrans('twill::lang.listing.bulk-publish.published', ['modelTitle' => $this->modelTitle]));
                } else {
                    return $this->respondWithSuccess(twillTrans('twill::lang.listing.bulk-publish.unpublished', ['modelTitle' => $this->modelTitle]));
                }
            }
        } catch (\Exception $e) {
            \Log::error($e);
        }

        return $this->respondWithError(twillTrans('twill::lang.listing.bulk-publish.error', ['modelTitle' => $this->modelTitle]));
    }

    /**
     * @param int $id
     * @param int|null $submoduleId
     * @return \Illuminate\Http\JsonResponse
     */
    public function duplicate($id, $submoduleId = null)
    {
        $params = $this->request->route()->parameters();

        $id = last($params);

        $item = $this->repository->getById($id);
        if ($newItem = $this->repository->duplicate($id, $this->titleColumnKey)) {
            $this->fireEvent();
            activity()->performedOn($item)->log('duplicated');

            return Response::json([
                'message' => twillTrans('twill::lang.listing.duplicate.success', ['modelTitle' => $this->modelTitle]),
                'variant' => FlashLevel::SUCCESS,
                'redirect' => moduleRoute(
                    $this->moduleName,
                    $this->routePrefix,
                    'edit',
                    array_filter([Str::singular($this->moduleName) => $newItem->id])
                ),
            ]);
        }

        return $this->respondWithError(twillTrans('twill::lang.listing.duplicate.error', ['modelTitle' => $this->modelTitle]));
    }

    /**
     * @param int $id
     * @param int|null $submoduleId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id, $submoduleId = null)
    {
        $params = $this->request->route()->parameters();

        $id = last($params);

        $item = $this->repository->getById($id);
        if ($this->repository->delete($id)) {
            $this->fireEvent();
            activity()->performedOn($item)->log('deleted');

            return $this->respondWithSuccess(twillTrans('twill::lang.listing.delete.success', ['modelTitle' => $this->modelTitle]));
        }

        return $this->respondWithError(twillTrans('twill::lang.listing.delete.error', ['modelTitle' => $this->modelTitle]));
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkDelete()
    {
        if ($this->repository->bulkDelete(explode(',', $this->request->get('ids')))) {
            $this->fireEvent();

            return $this->respondWithSuccess(twillTrans('twill::lang.listing.bulk-delete.success', ['modelTitle' => $this->modelTitle]));
        }

        return $this->respondWithError(twillTrans('twill::lang.listing.bulk-delete.error', ['modelTitle' => $this->modelTitle]));
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function forceDelete()
    {
        if ($this->repository->forceDelete($this->request->get('id'))) {
            $this->fireEvent();

            return $this->respondWithSuccess(twillTrans('twill::lang.listing.force-delete.success', ['modelTitle' => $this->modelTitle]));
        }

        return $this->respondWithError(twillTrans('twill::lang.listing.force-delete.error', ['modelTitle' => $this->modelTitle]));
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkForceDelete()
    {
        if ($this->repository->bulkForceDelete(explode(',', $this->request->get('ids')))) {
            $this->fireEvent();

            return $this->respondWithSuccess(twillTrans('twill::lang.listing.bulk-force-delete.success', ['modelTitle' => $this->modelTitle]));
        }

        return $this->respondWithError(twillTrans('twill::lang.listing.bulk-force-delete.error', ['modelTitle' => $this->modelTitle]));
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore()
    {
        if ($this->repository->restore($this->request->get('id'))) {
            $this->fireEvent();
            activity()->performedOn($this->repository->getById($this->request->get('id')))->log('restored');

            return $this->respondWithSuccess(twillTrans('twill::lang.listing.restore.success', ['modelTitle' => $this->modelTitle]));
        }

        return $this->respondWithError(twillTrans('twill::lang.listing.restore.error', ['modelTitle' => $this->modelTitle]));
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkRestore()
    {
        if ($this->repository->bulkRestore(explode(',', $this->request->get('ids')))) {
            $this->fireEvent();

            return $this->respondWithSuccess(twillTrans('twill::lang.listing.bulk-restore.success', ['modelTitle' => $this->modelTitle]));
        }

        return $this->respondWithError(twillTrans('twill::lang.listing.bulk-restore.error', ['modelTitle' => $this->modelTitle]));
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
                return $this->respondWithSuccess(twillTrans('twill::lang.listing.featured.unfeatured', ['modelTitle' => $this->modelTitle]));
            } else {
                return $this->respondWithSuccess(twillTrans('twill::lang.listing.featured.featured', ['modelTitle' => $this->modelTitle]));
            }
        }

        return $this->respondWithError(twillTrans('twill::lang.listing.featured.error', ['modelTitle' => $this->modelTitle]));
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
                return $this->respondWithSuccess(twillTrans('twill::lang.listing.bulk-featured.featured', ['modelTitle' => $this->modelTitle]));
            } else {
                return $this->respondWithSuccess(twillTrans('twill::lang.listing.bulk-featured.unfeatured', ['modelTitle' => $this->modelTitle]));
            }
        }

        return $this->respondWithError(twillTrans('twill::lang.listing.bulk-featured.error', ['modelTitle' => $this->modelTitle]));
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function reorder()
    {
        if (($values = $this->request->get('ids')) && ! empty($values)) {
            $this->repository->setNewOrder($values);
            $this->fireEvent();

            return $this->respondWithSuccess(twillTrans('twill::lang.listing.reorder.success', ['modelTitle' => $this->modelTitle]));
        }

        return $this->respondWithError(twillTrans('twill::lang.listing.reorder.error', ['modelTitle' => $this->modelTitle]));
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function tags()
    {
        $query = $this->request->input('q');
        $tags = $this->repository->getTags($query);

        return Response::json(['items' => $tags->map(function ($tag) {
            return $tag->name;
        })], 200);
    }

    /**
     * @return array
     */
    public function additionalTableActions()
    {
        return [];
    }

    /**
     * @param array $prependScope
     * @return array
     */
    protected function getIndexData($prependScope = [])
    {
        $scopes = $this->filterScope($prependScope);
        $items = $this->getIndexItems($scopes);

        $data = [
            'tableData' => $this->getIndexTableData($items),
            'tableColumns' => $this->getIndexTableColumns($items),
            'tableMainFilters' => $this->getIndexTableMainFilters($items),
            'filters' => json_decode($this->request->get('filter'), true) ?? [],
            'hiddenFilters' => array_keys(Arr::except($this->filters, array_keys($this->defaultFilters))),
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
            'baseUrl' => $baseUrl,
            'permalinkPrefix' => $this->getPermalinkPrefix($baseUrl),
            'additionalTableActions' => $this->additionalTableActions(),
        ];

        return array_replace_recursive($data + $options, $this->indexData($this->request));
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
     * @param array $scopes
     * @param bool $forcePagination
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getIndexItems($scopes = [], $forcePagination = false)
    {
        return $this->transformIndexItems($this->repository->get(
            $this->indexWith,
            $scopes,
            $this->orderScope(),
            $this->request->get('offset') ?? $this->perPage ?? 50,
            $forcePagination
        ));
    }

    /**
     * @param \Illuminate\Database\Eloquent\Collection $items
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function transformIndexItems($items)
    {
        return $items;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Collection $items
     * @return array
     */
    protected function getIndexTableData($items)
    {
        $translated = $this->moduleHas('translations');

        return $items->map(function ($item) use ($translated) {
            $columnsData = Collection::make($this->indexColumns)->mapWithKeys(function ($column) use ($item) {
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
            $canDuplicate = $this->getIndexOption('duplicate');

            $itemId = $this->getItemIdentifier($item);

            return array_replace([
                'id' => $itemId,
                'name' => $name,
                'publish_start_date' => $item->publish_start_date,
                'publish_end_date' => $item->publish_end_date,
                'edit' => $canEdit ? $this->getModuleRoute($itemId, 'edit') : null,
                'duplicate' => $canDuplicate ? $this->getModuleRoute($itemId, 'duplicate') : null,
                'delete' => $itemCanDelete ? $this->getModuleRoute($itemId, 'destroy') : null,
            ] + ($this->getIndexOption('editInModal') ? [
                'editInModal' => $this->getModuleRoute($itemId, 'edit'),
                'updateUrl' => $this->getModuleRoute($itemId, 'update'),
            ] : []) + ($this->getIndexOption('publish') && ($item->canPublish ?? true) ? [
                'published' => $item->published,
            ] : []) + ($this->getIndexOption('feature') && ($item->canFeature ?? true) ? [
                'featured' => $item->{$this->featureField},
            ] : []) + (($this->getIndexOption('restore') && $itemIsTrashed) ? [
                'deleted' => true,
            ] : []) + (($this->getIndexOption('forceDelete') && $itemIsTrashed) ? [
                'destroyable' => true,
            ] : []) + ($translated ? [
                'languages' => $item->getActiveLanguages(),
            ] : []) + $columnsData, $this->indexItemData($item));
        })->toArray();
    }

    /**
     * @param \A17\Twill\Models\Model $item
     * @return array
     */
    protected function indexItemData($item)
    {
        return [];
    }

    /**
     * @param \A17\Twill\Models\Model $item
     * @param array $column
     * @return array
     */
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
            $module = Str::singular(last(explode('.', $this->moduleName)));
            $value = '<a href="';
            $value .= moduleRoute("$this->moduleName.$field", $this->routePrefix, 'index', [$module => $this->getItemIdentifier($item)]);
            $value .= '">' . $nestedCount . ' ' . (strtolower(Str::plural($column['title'], $nestedCount))) . '</a>';
        } else {
            $field = $column['field'];
            $value = $item->$field;
        }

        if (isset($column['relationship'])) {
            $field = $column['relationship'] . ucfirst($column['field']);

            $relation = $item->{$column['relationship']}();

            $value = collect($relation->get())
                ->pluck($column['field'])
                ->join(', ');
        } elseif (isset($column['present']) && $column['present']) {
            $value = $item->presentAdmin()->{$column['field']};
        }

        if (isset($column['relatedBrowser']) && $column['relatedBrowser']) {
            $field = 'relatedBrowser' . ucfirst($column['relatedBrowser']) . ucfirst($column['field']);
            $value = $item->getRelated($column['relatedBrowser'])
                ->pluck($column['field'])
                ->join(', ');
        }

        return [
            "$field" => $value,
        ];
    }

    /**
     * @param \A17\Twill\Models\Model $item
     * @return int|string
     */
    protected function getItemIdentifier($item)
    {
        return $item->{$this->identifierColumnKey};
    }

    /**
     * @param \Illuminate\Database\Eloquent\Collection $items
     * @return array
     */
    protected function getIndexTableColumns($items)
    {
        $tableColumns = [];
        $visibleColumns = $this->request->get('columns') ?? false;
        $indexColumnCopy = $this->indexColumns;

        if (isset(Arr::first($indexColumnCopy)['thumb'])
            && Arr::first($indexColumnCopy)['thumb']
        ) {
            $tableColumns[] = [
                'name' => 'thumbnail',
                'label' => twillTrans('twill::lang.listing.columns.thumbnail'),
                'visible' => $visibleColumns ? in_array('thumbnail', $visibleColumns) : true,
                'optional' => true,
                'sortable' => false,
            ];
            array_shift($indexColumnCopy);
        }

        if ($this->getIndexOption('feature')) {
            $tableColumns[] = [
                'name' => 'featured',
                'label' => twillTrans('twill::lang.listing.columns.featured'),
                'visible' => true,
                'optional' => false,
                'sortable' => false,
            ];
        }

        if ($this->getIndexOption('publish')) {
            $tableColumns[] = [
                'name' => 'published',
                'label' => twillTrans('twill::lang.listing.columns.published'),
                'visible' => true,
                'optional' => false,
                'sortable' => false,
            ];
        }

        $tableColumns[] = [
            'name' => 'name',
            'label' => $indexColumnCopy[$this->titleColumnKey]['title'] ?? twillTrans('twill::lang.listing.columns.name'),
            'visible' => true,
            'optional' => false,
            'sortable' => $this->getIndexOption('reorder') ? false : ($indexColumnCopy[$this->titleColumnKey]['sort'] ?? false),
        ];

        unset($indexColumnCopy[$this->titleColumnKey]);

        foreach ($indexColumnCopy as $column) {
            if (isset($column['relationship'])) {
                $columnName = $column['relationship'] . ucfirst($column['field']);
            } elseif (isset($column['nested'])) {
                $columnName = $column['nested'];
            } elseif (isset($column['relatedBrowser'])) {
                $columnName = 'relatedBrowser' . ucfirst($column['relatedBrowser']) . ucfirst($column['field']);
            } else {
                $columnName = $column['field'];
            }

            $tableColumns[] = [
                'name' => $columnName,
                'label' => $column['title'],
                'visible' => $visibleColumns ? in_array($columnName, $visibleColumns) : ($column['visible'] ?? true),
                'optional' => $column['optional'] ?? true,
                'sortable' => $this->getIndexOption('reorder') ? false : ($column['sort'] ?? false),
                'html' => $column['html'] ?? false,
            ];
        }

        if ($this->getIndexOption('includeScheduledInList') && $this->repository->isFillable('publish_start_date')) {
            $tableColumns[] = [
                'name' => 'publish_start_date',
                'label' => twillTrans('twill::lang.listing.columns.published'),
                'visible' => true,
                'optional' => true,
                'sortable' => true,
            ];
        }

        if ($this->moduleHas('translations') && count(getLocales()) > 1) {
            $tableColumns[] = [
                'name' => 'languages',
                'label' => twillTrans('twill::lang.listing.languages'),
                'visible' => $visibleColumns ? in_array('languages', $visibleColumns) : true,
                'optional' => true,
                'sortable' => false,
            ];
        }

        return $tableColumns;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Collection $items
     * @param array $scopes
     * @return array
     */
    protected function getIndexTableMainFilters($items, $scopes = [])
    {
        $statusFilters = [];

        $scope = ($this->submodule ? [
            $this->getParentModuleForeignKey() => $this->submoduleParentId,
        ] : []) + $scopes;

        $statusFilters[] = [
            'name' => twillTrans('twill::lang.listing.filter.all-items'),
            'slug' => 'all',
            'number' => $this->repository->getCountByStatusSlug('all', $scope),
        ];

        if ($this->moduleHas('revisions') && $this->getIndexOption('create')) {
            $statusFilters[] = [
                'name' => twillTrans('twill::lang.listing.filter.mine'),
                'slug' => 'mine',
                'number' => $this->repository->getCountByStatusSlug('mine', $scope),
            ];
        }

        if ($this->getIndexOption('publish')) {
            $statusFilters[] = [
                'name' => twillTrans('twill::lang.listing.filter.published'),
                'slug' => 'published',
                'number' => $this->repository->getCountByStatusSlug('published', $scope),
            ];
            $statusFilters[] = [
                'name' => twillTrans('twill::lang.listing.filter.draft'),
                'slug' => 'draft',
                'number' => $this->repository->getCountByStatusSlug('draft', $scope),
            ];
        }

        if ($this->getIndexOption('restore')) {
            $statusFilters[] = [
                'name' => twillTrans('twill::lang.listing.filter.trash'),
                'slug' => 'trash',
                'number' => $this->repository->getCountByStatusSlug('trash', $scope),
            ];
        }

        return $statusFilters;
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
                'duplicate' => 'duplicate',
                'restore' => 'delete',
                'forceDelete' => 'delete',
                'bulkForceDelete' => 'delete',
                'bulkPublish' => 'publish',
                'bulkRestore' => 'delete',
                'bulkFeature' => 'feature',
                'bulkDelete' => 'delete',
                'bulkEdit' => 'edit',
                'editInModal' => 'edit',
                'skipCreateModal' => 'edit',
            ];

            $authorized = array_key_exists($option, $authorizableOptions) ? Auth::guard('twill_users')->user()->can($authorizableOptions[$option]) : true;

            return ($this->indexOptions[$option] ?? $this->defaultIndexOptions[$option] ?? false) && $authorized;
        });
    }

    /**
     * @param array $prependScope
     * @return array
     */
    protected function getBrowserData($prependScope = [])
    {
        if ($this->request->has('except')) {
            $prependScope['exceptIds'] = $this->request->get('except');
        }

        $scopes = $this->filterScope($prependScope);
        $items = $this->getBrowserItems($scopes);
        $data = $this->getBrowserTableData($items);

        return array_replace_recursive(['data' => $data], $this->indexData($this->request));
    }

    /**
     * @param \Illuminate\Database\Eloquent\Collection $items
     * @return array
     */
    protected function getBrowserTableData($items)
    {
        $withImage = $this->moduleHas('medias');

        return $items->map(function ($item) use ($withImage) {
            $columnsData = Collection::make($this->browserColumns)->mapWithKeys(function ($column) use ($item) {
                return $this->getItemColumnData($item, $column);
            })->toArray();

            $name = $columnsData[$this->titleColumnKey];
            unset($columnsData[$this->titleColumnKey]);

            return [
                'id' => $this->getItemIdentifier($item),
                'name' => $name,
                'edit' => moduleRoute($this->moduleName, $this->routePrefix, 'edit', $this->getItemIdentifier($item)),
                'endpointType' => $this->repository->getMorphClass(),
            ] + $columnsData + ($withImage && ! array_key_exists('thumbnail', $columnsData) ? [
                'thumbnail' => $item->defaultCmsImage(['w' => 100, 'h' => 100]),
            ] : []);
        })->toArray();
    }

    /**
     * @param array $scopes
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getBrowserItems($scopes = [])
    {
        return $this->getIndexItems($scopes, true);
    }

    /**
     * @param array $prepend
     * @return array
     */
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
                if ($value == 0 || ! empty($value)) {
                    // add some syntaxic sugar to scope the same filter on multiple columns
                    $fieldSplitted = explode('|', $field);
                    if (count($fieldSplitted) > 1) {
                        $requestValue = $requestFilters[$key];
                        Collection::make($fieldSplitted)->each(function ($scopeKey) use (&$scope, $requestValue) {
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

    /**
     * @return array
     */
    protected function getRequestFilters()
    {
        if ($this->request->has('search')) {
            return ['search' => $this->request->get('search')];
        }

        return json_decode($this->request->get('filter'), true) ?? [];
    }

    /**
     * @return void
     */
    protected function applyFiltersDefaultOptions()
    {
        if (! count($this->filtersDefaultOptions) || $this->request->has('search')) {
            return;
        }

        $filters = $this->getRequestFilters();

        foreach ($this->filtersDefaultOptions as $filterName => $defaultOption) {
            if (! isset($filters[$filterName])) {
                $filters[$filterName] = $defaultOption;
            }
        }

        $this->request->merge(['filter' => json_encode($filters)]);
    }

    /**
     * @return array
     */
    protected function orderScope()
    {
        $orders = [];
        if ($this->request->has('sortKey') && $this->request->has('sortDir')) {
            if (($key = $this->request->get('sortKey')) == 'name') {
                $sortKey = $this->titleColumnKey;
            } elseif (! empty($key)) {
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

    /**
     * @param int $id
     * @param \A17\Twill\Models\Model|null $item
     * @return array
     */
    protected function form($id, $item = null)
    {
        if (! $item && $id) {
            $item = $this->repository->getById($id, $this->formWith, $this->formWithCount);
        } elseif (! $item && ! $id) {
            $item = $this->repository->newInstance();
        }

        $fullRoutePrefix = 'admin.' . ($this->routePrefix ? $this->routePrefix . '.' : '') . $this->moduleName . '.';
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
            'publish' => $item->canPublish ?? true,
            'publishDate24Hr' => Config::get('twill.publish_date_24h') ?? false,
            'publishDateFormat' => Config::get('twill.publish_date_format') ?? null,
            'publishDateDisplayFormat' => Config::get('twill.publish_date_display_format') ?? null,
            'translate' => $this->moduleHas('translations'),
            'translateTitle' => $this->titleIsTranslatable(),
            'permalink' => $this->getIndexOption('permalink'),
            'createWithoutModal' => ! $itemId && $this->getIndexOption('skipCreateModal'),
            'form_fields' => $this->repository->getFormFields($item),
            'baseUrl' => $baseUrl,
            'localizedPermalinkBase'=>$localizedPermalinkBase,
            'permalinkPrefix' => $this->getPermalinkPrefix($baseUrl),
            'saveUrl' => $itemId ? $this->getModuleRoute($itemId, 'update') : moduleRoute($this->moduleName, $this->routePrefix, 'store', [$this->submoduleParentId]),
            'editor' => Config::get('twill.enabled.block-editor') && $this->moduleHas('blocks') && ! $this->disableEditor,
            'blockPreviewUrl' => Route::has('admin.blocks.preview') ? URL::route('admin.blocks.preview') : '#',
            'availableRepeaters' => $this->getRepeaterList()->toJson(),
            'revisions' => $this->moduleHas('revisions') ? $item->revisionsArray() : null,
        ] + (Route::has($previewRouteName) && $itemId ? [
            'previewUrl' => moduleRoute($this->moduleName, $this->routePrefix, 'preview', [$itemId]),
        ] : [])
             + (Route::has($restoreRouteName) && $itemId ? [
            'restoreUrl' => moduleRoute($this->moduleName, $this->routePrefix, 'restoreRevision', [$itemId]),
        ] : []);

        return array_replace_recursive($data, $this->formData($this->request));
    }

    /**
     * @param int $id
     * @return array
     */
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

    /**
     * @param Request $request
     * @return array
     */
    protected function formData($request)
    {
        return [];
    }

    /**
     * @param Request $item
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
        $request = "$this->namespace\Http\Requests\Admin\\" . $this->modelName . 'Request';

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
            $routePrefix = ltrim(str_replace(Config::get('twill.admin_app_path'), '', $this->request->route()->getPrefix()), '/');

            return str_replace('/', '.', $routePrefix);
        }

        return '';
    }

    /**
     * @return string
     */
    protected function getModulePermalinkBase()
    {
        $base = '';
        $moduleParts = explode('.', $this->moduleName);

        foreach ($moduleParts as $index => $name) {
            if (array_key_last($moduleParts) !== $index) {
                $singularName = Str::singular($name);
                $modelClass = config('twill.namespace') . '\\Models\\' . Str::studly($singularName);

                if (! class_exists($modelClass)) {
                    $modelClass = $this->getCapsuleByModule($name)['model'];
                }

                $model = (new $modelClass())->findOrFail(request()->route()->parameter($singularName));
                $hasSlug = Arr::has(class_uses($modelClass), HasSlug::class);

                $base .= $name . '/' . ($hasSlug ? $model->slug : $model->id) . '/';
            } else {
                $base .= $name;
            }
        }

        return $base;
    }

    /**
     * @return string
     */
    protected function getModelName()
    {
        return $this->modelName ?? ucfirst(Str::singular($this->moduleName));
    }

    /**
     * @return \A17\Twill\Repositories\ModuleRepository
     */
    protected function getRepository()
    {
        return App::make($this->getRepositoryClass($this->modelName));
    }

    public function getRepositoryClass($model)
    {
        if (@class_exists($class = "$this->namespace\Repositories\\" . $model . 'Repository')) {
            return $class;
        }

        return TwillCapsules::getCapsuleForModel($model)->getRepositoryClass();
    }

    protected function getViewPrefix(): ?string
    {
        $prefix = "admin.$this->moduleName";

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
    protected function getPermalinkBaseUrl()
    {
        $appUrl = Config::get('app.url');

        if (blank(parse_url($appUrl)['scheme'] ?? null)) {
            $appUrl = $this->request->getScheme() . '://' . $appUrl;
        }

        return $appUrl . '/'
            . ($this->moduleHas('translations') ? '{language}/' : '')
            . ($this->moduleHas('revisions') ? '{preview}/' : '')
            . (empty($this->getLocalizedPermalinkBase()) ? ($this->permalinkBase ?? $this->getModulePermalinkBase()) : '')
            . (((isset($this->permalinkBase) && empty($this->permalinkBase)) || ! empty($this->getLocalizedPermalinkBase())) ? '' : '/');
    }

    /**
     * @return array
     */
    protected function getLocalizedPermalinkBase()
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

        return Redirect::to(moduleRoute(
            $this->moduleName,
            $this->routePrefix,
            'edit',
            array_filter($params) + [Str::singular($this->moduleName) => $id]
        ));
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

    /**
     * @param array $input
     * @return void
     */
    protected function fireEvent($input = [])
    {
        fireCmsEvent('cms-module.saved', $input);
    }

    /**
     * @return Collection|Block[]
     */
    public function getRepeaterList()
    {
        return TwillBlocks::getBlockCollection()->getRepeaters()->mapWithKeys(function (Block $repeater) {
            return [$repeater->name => $repeater->toList()];
        });
    }
}
