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
            if (config('twill.auto_seed_singletons', false)) {
                $this->seed();
                return $this->editSingleton();
            }
            throw new \Exception("$model is not seeded");
        }

        Session::put('pages_back_link', url()->current());

        return view("twill.{$this->moduleName}.form", $this->form($item->id));
    }

    private function seed(): void
    {
        $seederName = '\\Database\\Seeders\\' . $this->getModelName() . 'Seeder';
        if (!class_exists($seederName)) {
            throw new \Exception("$seederName is missing");
        }
        $seeder = new $seederName();
        $seeder->run();
    }
}
