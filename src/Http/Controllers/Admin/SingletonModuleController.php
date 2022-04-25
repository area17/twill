<?php

namespace A17\Twill\Http\Controllers\Admin;

use A17\Twill\Facades\TwillCapsules;
use Illuminate\Support\Facades\Session;

abstract class SingletonModuleController extends ModuleController
{
    /**
     * @var string
     */
    protected $permalinkBase = '';

    /**
     * @return never
     */
    public function index($parentModuleId = null)
    {
        throw new \Exception(sprintf('%s has no index', $this->getModelName()));
    }

    public function editSingleton()
    {
        $model = sprintf('App\Models\%s', $this->getModelName());

        if (!class_exists($model)) {
            $model = TwillCapsules::getCapsuleForModel($this->modelName)->getModel();
        }

        $item = app($model)->first();

        if (!$item) {
            if (config('twill.auto_seed_singletons', false)) {
                $this->seed();
                return $this->editSingleton();
            }

            throw new \Exception(sprintf('%s is not seeded', $model));
        }

        Session::put('pages_back_link', url()->current());

        return view(sprintf('twill.%s.form', $this->moduleName), $this->form($item->id));
    }

    private function seed(): void
    {
        $seederName = '\\Database\\Seeders\\' . $this->getModelName() . 'Seeder';
        if (!class_exists($seederName)) {
            throw new \Exception(sprintf('%s is missing', $seederName));
        }

        $seeder = new $seederName();
        $seeder->run();
    }
}
