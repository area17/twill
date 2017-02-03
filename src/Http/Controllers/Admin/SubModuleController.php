<?php

namespace A17\CmsToolkit\Http\Controllers\Admin;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;

abstract class SubModuleController extends ModuleController
{

    protected $parentRepository;

    protected $parentBreadcrumbField = 'title';

    protected $breadcrumb = true;

    public function __construct(Application $app, Request $request)
    {
        $this->app = $app;
        $this->request = $request;

        $this->setMiddlewarePermission();

        $this->modelName = $this->getModelName();
        $this->routePrefix = $this->getRoutePrefix();
        $this->namespace = $this->getNamespace();
        $this->repository = $this->getRepository();

        $parentModelName = ucfirst(str_singular(explode('.', $this->moduleName)[0]));

        $this->parentRepository = $this->app->make("$this->namespace\Repositories\\" . $parentModelName . "Repository");
    }

    public function index($parent_id = null)
    {
        $params = [];
        if ($parent_id != null) {
            $params['parent_id'] = $parent_id;

            $this->setBackLink(moduleRoute(explode('.', $this->moduleName)[0], $this->routePrefix, "index"));
            $params['back_link'] = $this->getBackLink();
        }
        $params['breadcrumb'] = $this->getBreadcrumbParent($parent_id, null);

        return view("admin.{$this->moduleName}.index", $this->getIndexData(isset($parent_id) ? [$this->getParentNameField() => $parent_id] : []) + $this->request->all() + $params);
    }

    protected function getParentNameField()
    {
        return str_singular(explode('.', $this->moduleName)[0]) . '_id';
    }

    public function create($parent_id = null)
    {
        $this->setBackLink();

        $data = [
            'form_options' => [
                'method' => 'POST',
                'url' => moduleRoute($this->moduleName, $this->routePrefix, 'store', [$parent_id]),
            ] + $this->defaultFormOptions(),
            'form_fields' => $this->repository->getOldFormFieldsOnCreate(),
            'back_link' => $this->getBackLink(),
            'moduleName' => $this->moduleName,
            'modelName' => $this->modelName,
            'routePrefix' => $this->routePrefix,
            'parent_id' => $parent_id,
        ];

        return view("admin.{$this->moduleName}.form", array_replace_recursive($data, $this->formData($this->request, $parent_id)));
    }

    public function store($parent_id = null)
    {
        $formRequest = $this->app->make("$this->namespace\Http\Requests\Admin\\" . $this->modelName . "Request");
        $item = $this->repository->create($formRequest->all() + [$this->getParentNameField() => $parent_id]);
        return $this->redirectToForm($item->id, [$parent_id]);
    }

    public function show($parent_id = null, $id = null)
    {
        return $this->redirectToForm($id);
    }

    public function edit($parent_id = null, $id = null)
    {
        $this->setBackLink();
        return view("admin.{$this->moduleName}.form", $this->form($id, $parent_id));
    }

    private function form($id, $parent_id = null)
    {
        $item = $this->repository->getById($id, $this->formWith);
        $data = [
            'form_options' => [
                'method' => 'PUT',
                'url' => moduleRoute($this->moduleName, $this->routePrefix, 'update', array_merge(isset($parent_id) ? [$parent_id] : [], [$id])),
            ] + $this->defaultFormOptions(),
            'item' => $item,
            'form_fields' => $this->repository->getFormFields($item),
            'back_link' => $this->getBackLink(),
            'moduleName' => $this->moduleName,
            'modelName' => $this->modelName,
            'routePrefix' => $this->routePrefix,
            'breadcrumb' => $this->getBreadcrumbParent($parent_id, $id),
        ];

        return array_replace_recursive($data, $this->formData($this->request, $parent_id));
    }

    public function update($parent_id = null, $id = null)
    {
        $formRequest = $this->app->make("$this->namespace\Http\Requests\Admin\\" . $this->modelName . "Request");
        $this->repository->update($id, $formRequest->all());
        return $this->redirectToForm($id, [$parent_id]);
    }

    protected function getBreadcrumbParent($parent_id, $item_id, $append = [])
    {
        $breadcrumb = [];

        if (!$parent_id) {
            return $breadcrumb;
        }

        $nameModules = explode('.', $this->moduleName);

        $breadcrumb["All " . ucfirst($nameModules[0])] = moduleRoute($nameModules[0], $this->routePrefix, "index");

        if (($obj = $this->parentRepository->getById($parent_id)) !== null && isset($obj->{$this->parentBreadcrumbField})) {
            $breadcrumb[$obj->{$this->parentBreadcrumbField}] = null;
        }

        if ($item_id) {
            $breadcrumb["All " . ucfirst($nameModules[1])] = moduleRoute($this->moduleName, $this->routePrefix, "index", [$parent_id]);
        }

        return $breadcrumb + $append;
    }

    protected function getRoutePrefix()
    {
        return ($this->request->route() != null ? ltrim($this->request->route()->getPrefix(), "/") : '');
    }

    protected function getModelName()
    {
        return $this->modelName ?? implode("", array_map(function ($s) {
            return ucfirst(str_singular($s));
        }, explode('.', $this->moduleName)));
    }

}
