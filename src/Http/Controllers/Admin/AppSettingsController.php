<?php

namespace A17\Twill\Http\Controllers\Admin;

use A17\Twill\Facades\TwillAppSettings;
use A17\Twill\Http\Requests\Admin\Request;
use A17\Twill\Models\AppSetting;
use A17\Twill\Models\Contracts\TwillModelContract;
use A17\Twill\Repositories\AppSettingsRepository;
use A17\Twill\Services\Forms\Fields\BlockEditor;
use A17\Twill\Services\Forms\Form;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
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

    public function update(int|TwillModelContract $id, ?int $submoduleId = null): JsonResponse
    {
        $model = AppSetting::findOrFail($id);

        $model->registerSettingBlocks();

        return parent::update($id, $submoduleId);
    }

    public function editSettings(string $group)
    {
        $settingsGroup = TwillAppSettings::getGroupForName($group);

        if (!$settingsGroup->isAvailable()) {
            abort(403);
        }

        $model = AppSetting::where('name', '=', $group)->firstOrFail();

        $model->registerSettingBlocks();

        return $this->edit($model);
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

    protected function getModuleRoute($id, $action): ?string
    {
        if ($action === 'update') {
            return route('twill.app.settings.update', $id);
        }

        return null;
    }

    protected function setBackLink($back_link = null, $params = []): void
    {
        Session::put($this->moduleName . '_retain', $this->getBackLink());
    }

    protected function getBackLink($fallback = null, $params = []): ?string
    {
        return null;
    }

    public function getFormRequestClass(): string
    {
        $class = new class () extends Request {
        };

        return $class::class;
    }

    protected function getRepositoryClass($model): string
    {
        return AppSettingsRepository::class;
    }

    protected function form(?int $id, ?TwillModelContract $item = null): array
    {
        $base = parent::form($id, $item);

        $base['customTitle'] = $base['item']->getSettingGroup()->getLabel();

        $base['publish'] = false;
        $base['editableTitle'] = false;
        $base['translate'] = true;

        return $base;
    }

    public function getSubmitOptions(Model $item): ?array
    {

        return [
            'update' => [
                [
                    'name' => 'update',
                    'text' => twillTrans('twill::lang.publisher.update'),
                ],
            ],
        ];
    }
}
