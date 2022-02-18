<?php

namespace A17\Twill\Http\Controllers\Admin;

use A17\Twill\Facades\TwillCapsules;
use Illuminate\Support\Facades\Session;

abstract class SingletonModuleController extends ModuleController
{
    protected $permalinkBase = '';

    public function index($parentModuleId = null)
    {
        throw new \Exception("{$this->getModelName()} has no index");
    }

    public function editSingleton()
    {
        $model = "App\\Models\\{$this->getModelName()}";

        if (!class_exists($model)) {
            $model = TwillCapsules::getCapsuleForModel($this->modelName)->getModel();
        }

        $item = app($model)->first();

        if (!$item) {
            throw new \Exception("{$this->getModelName()} is missing");
        }

        Session::put('pages_back_link', url()->current());

        return view($this->viewPrefix . ".form", $this->form($item->id));
    }
}
