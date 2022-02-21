<?php

namespace A17\Twill\Http\Controllers\Admin;

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

        $item = app($model)->first();

        if (!$item) {
            $this->seed();
            return $this->editSingleton();
        }

        Session::put('pages_back_link', url()->current());

        return view("admin.{$this->moduleName}.form", $this->form($item->id));
    }

    private function seed(): void {
        $seederName = '\\Database\\Seeders\\' . $this->getModelName() . 'Seeder';
        if (!class_exists($seederName)) {
            throw new \Exception("$seederName is missing");
        }
        $seeder = new $seederName();
        $seeder->run();
    }
}
