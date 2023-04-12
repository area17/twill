<?php

namespace A17\Twill\Http\Controllers\Admin;

use A17\Twill\Facades\TwillCapsules;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

abstract class SingletonModuleController extends ModuleController
{
    protected $permalinkBase = '';

    public function index(?int $parentModuleId = null): mixed
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

        $controllerForm = $this->getForm($item);

        if ($controllerForm->hasForm()) {
            $view = 'twill::layouts.form';
        } else {
            $view = "twill.{$this->moduleName}.form";
        }

        View::share('form', $this->form($item->id));

        return View::make($view, $this->form($item->id))->with(
            ['formBuilder' => $controllerForm->toFrontend($this->getSideFieldsets($item))]
        );
    }

    private function seed(): void
    {
        $seederName = $this->getModelName() . 'Seeder';
        $seederNamespace = '\\Database\\Seeders\\';

        if (!class_exists($seederNamespace . $seederName)) {
            $seederNamespace = TwillCapsules::getCapsuleForModel($this->modelName)->getSeedsNamespace() . '\\';
        }

        $seederClass = $seederNamespace . $seederName;

        if (!class_exists($seederClass)) {
            throw new \Exception("$seederClass is missing");
        }

        $seeder = new $seederClass();
        $seeder->run();
    }

    public function getSubmitOptions(Model $item): ?array
    {
        if ($item->cmsRestoring ?? false) {
            return [
                'draft' => [
                    [
                        'name' => 'restore',
                        'text' => twillTrans('twill::lang.publisher.restore-draft'),
                    ],
                    [
                        'name' => 'cancel',
                        'text' => twillTrans('twill::lang.publisher.cancel'),
                    ],
                ],
                'live' => [
                    [
                        'name' => 'restore',
                        'text' => twillTrans('twill::lang.publisher.restore-live'),
                    ],
                    [
                        'name' => 'cancel',
                        'text' => twillTrans('twill::lang.publisher.cancel'),
                    ],
                ],
                'update' => [
                    [
                        'name' => 'restore',
                        'text' => twillTrans('twill::lang.publisher.restore-live'),
                    ],
                    [
                        'name' => 'cancel',
                        'text' => twillTrans('twill::lang.publisher.cancel'),
                    ],
                ],
            ];
        }

        return [
            'draft' => [
                [
                    'name' => 'save',
                    'text' => twillTrans('twill::lang.publisher.save'),
                ],
                [
                    'name' => 'cancel',
                    'text' => twillTrans('twill::lang.publisher.cancel'),
                ],
            ],
            'live' => [
                [
                    'name' => 'publish',
                    'text' => twillTrans('twill::lang.publisher.publish'),
                ],
                [
                    'name' => 'cancel',
                    'text' => twillTrans('twill::lang.publisher.cancel'),
                ],
            ],
            'update' => [
                [
                    'name' => 'update',
                    'text' => twillTrans('twill::lang.publisher.update'),
                ],
                [
                    'name' => 'cancel',
                    'text' => twillTrans('twill::lang.publisher.cancel'),
                ],
            ],
        ];
    }
}
