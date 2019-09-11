<?php

namespace A17\Twill\Repositories;

use A17\Twill\Models\Setting;
use A17\Twill\Repositories\Behaviors\HandleMedias;
use Illuminate\Config\Repository as Config;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class SettingRepository extends ModuleRepository
{
    use HandleMedias;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @param Setting $model
     * @param Config $config
     */
    public function __construct(Setting $model, Config $config)
    {
        $this->model = $model;
        $this->config = $config;
    }

    /**
     * @param string $key
     * @param string|null $section
     * @return string|null
     */
    public function byKey($key, $section = null)
    {
        return $this->model->when($section, function ($query) use ($section) {
            $query->where('section', $section);
        })->where('key', $key)->exists() ? $this->model->where('key', $key)->with('translations')->first()->value : null;
    }

    /**
     * @param string|null $section
     * @return array
     */
    public function getFormFields($section = null)
    {
        $settings = $this->model->when($section, function ($query) use ($section) {
            $query->where('section', $section);
        })->with('translations', 'medias')->get();

        $medias = $settings->mapWithKeys(function ($setting) {
            return [$setting->key => parent::getFormFields($setting)['medias'][$setting->key] ?? null];
        })->filter()->toArray();

        return $settings->mapWithKeys(function ($setting) {
            $settingValue = [];

            foreach ($setting->translations as $translation) {
                $settingValue[$translation->locale] = $translation->value;
            }

            return [$setting->key => count(getLocales()) > 1 ? $settingValue : $setting->value];
        })->toArray() + ['medias' => $medias];
    }

    /**
     * @param array $settingsFields
     * @param string|null $section
     * @return void
     */
    public function saveAll($settingsFields, $section = null)
    {
        $section = $section ? ['section' => $section] : [];

        foreach (Collection::make($settingsFields)->except('active_languages', 'medias', 'mediaMeta', 'update') as $key => $value) {
            foreach (getLocales() as $locale) {
                Arr::set(
                    $settingsTranslated,
                    $key . '.' . $locale,
                    [
                        'value' => is_array($value)
                        ? (array_key_exists($locale, $value) ? $value[$locale] : $value)
                        : $value,
                    ] + ['active' => true]
                );
            }
        }

        if (isset($settingsTranslated) && !empty($settingsTranslated)) {
            foreach ($settingsTranslated as $key => $values) {
                Arr::set($settings, $key, ['key' => $key] + $section + $values);
            }

            foreach ($settings as $key => $setting) {
                $this->model->updateOrCreate(['key' => $key] + $section, $setting);
            }
        }

        foreach ($settingsFields['medias'] ?? [] as $role => $mediasList) {
            $this->updateOrCreate($section + ['key' => $role], $section + [
                'key' => $role,
                'medias' => [
                    $role => Collection::make($settingsFields['medias'][$role])->map(function ($media) {
                        return json_decode($media, true);
                    })->values()->filter()->toArray(),
                ],
            ]);
        }
    }

    /**
     * @param string $role
     * @return array
     */
    public function getCrops($role)
    {
        return $this->config->get('twill.settings.crops')[$role];
    }

}
