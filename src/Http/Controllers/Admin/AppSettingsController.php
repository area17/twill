<?php

namespace A17\Twill\Http\Controllers\Admin;

use A17\Twill\Facades\TwillAppSettings;
use A17\Twill\Models\AppSetting;
use A17\Twill\Models\Contracts\TwillModelContract;
use A17\Twill\Repositories\AppSettingsRepository;
use A17\Twill\Services\Forms\Fields\BlockEditor;
use A17\Twill\Services\Forms\Form;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Session;

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

        foreach (TwillAppSettings::getAllGroups() as $group) {
            $group->boot();
        }
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
                    ->blocks(['appSettings' . '.' . $model->getSettingGroup()->getName() . '.' . $name])
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

    protected function setBackLink($back_link = null, $params = [])
    {
        Session::put($this->moduleName . '_retain', $this->getBackLink());
    }

    protected function getBackLink($fallback = null, $params = [])
    {
        return route('twill.app.settings');
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
