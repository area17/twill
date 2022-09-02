<?php

namespace A17\Twill\Http\Controllers\Admin;

use A17\Twill\Facades\TwillAppSettings;
use A17\Twill\Models\AppSetting;
use A17\Twill\Models\Contracts\TwillModelContract;
use A17\Twill\Repositories\AppSettingsRepository;
use A17\Twill\Services\Forms\Fields\BlockEditor;
use A17\Twill\Services\Forms\Form;
use Illuminate\Contracts\View\View;

/**
 * The name is a bit stupid here, but we do have the legacy settings (deprecate in 4.x?).
 */
class AppSettingsController extends ModuleController
{
    protected $moduleName = 'appSettings';

    protected function setUpController(): void
    {
        $this->disablePermalink();
        $this->disableEditor();
        $this->disableCreate();
        $this->disablePublish();
        $this->setTitleColumnKey('name');

        $this->seedModelsAndRegisterBlocks();
    }

    public function update($id, $submoduleId = null)
    {
        $model = AppSetting::findOrFail($id);

        $model->registerSettingBlocks();

        return parent::update($id, $submoduleId);
    }

    public function editSettings(string $group)
    {
        $model = AppSetting::where('name', '=', $group)->firstOrFail();

        $model->registerSettingBlocks();

        return parent::edit($model);
    }

    /**
     * This makes sure that the base models exist for our settings to work. This is based on the directories inside
     * the settings folder.
     */
    protected function seedModelsAndRegisterBlocks(): void
    {
        foreach (TwillAppSettings::getAllGroups() as $group) {
            $settingsModel = AppSetting::where(['name' => $group->getName()])->first();
            if (! $settingsModel) {
                $settingsModel = AppSetting::create(['name' => $group->getName()]);
            }

            // Ensure all the base blocks are there.
            foreach ($settingsModel->getFormBlocks() as $name) {
                $this->createBlockIfNotExisting($name, $settingsModel);
            }
        }
    }

    public function getForm(TwillModelContract|AppSetting $model): Form
    {
        // 1. Get our settings sections (for now blocks).
        $form = new Form();

        foreach ($model->getFormBlocks() as $name) {
            $form->add(
                BlockEditor::make()
                    ->name($name)
                    ->withoutSeparator()
                    ->isSettings()
                    ->blocks(['appSettings' . '.' . $model->getDirName() . '.' . $name])
            );
        }

        return $form;
    }

    protected function getModuleRoute($id, $action)
    {
        if ($action === 'update') {
            return route('twill.app.settings.update', $id);
        }

        return null;
    }

    private function createBlockIfNotExisting(string $name, AppSetting|TwillModelContract $model): void
    {
        if (! $model->blocks()->where('editor_name', '=', $name)->exists()) {
            $model->blocks()->create([
                'editor_name' => $name,
                'type' => $this->moduleName . '.' . $model->getDirName() . '.' . $name,
                'content' => [],
                'position' => 1,
            ]);
        }
    }

    public function dashboard(): View
    {
        return view('twill::settings.dashboard', ['groups' => TwillAppSettings::getGroupsForNavigation()]);
    }

    public function getFormRequestClass()
    {
        $class = new class() extends \A17\Twill\Http\Requests\Admin\Request {
        };

        return $class::class;
    }

    protected function getRepositoryClass($model): string
    {
        return AppSettingsRepository::class;
    }
}
