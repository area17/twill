<?php

namespace A17\Twill\Repositories;

use A17\Twill\Models\Setting;
use A17\Twill\Repositories\Behaviors\HandleMedias;

class SettingRepository extends ModuleRepository
{
    use HandleMedias;

    public function __construct(Setting $model)
    {
        $this->model = $model;
    }

    public function byKey($key, $section = null)
    {
        return $this->model->when($section, function ($query) use ($section) {
            $query->where('section', $section);
        })->where('key', $key)->exists() ? $this->model->where('key', $key)->with('translations')->first()->value : null;
    }

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

    public function saveAll($settingsFields, $section = null)
    {
        $section = $section ? ['section' => $section] : [];

        foreach (collect($settingsFields)->except('active_languages', 'medias', 'mediaMeta')->filter() as $key => $value) {
            foreach (getLocales() as $locale) {
                array_set(
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

        foreach ($settingsTranslated as $key => $values) {
            array_set($settings, $key, ['key' => $key] + $section + $values);
        }

        foreach ($settings as $key => $setting) {
            $this->model->updateOrCreate(['key' => $key] + $section, $setting);
        }

        foreach ($settingsFields['medias'] ?? [] as $role => $mediasList) {
            $this->updateOrCreate($section + ['key' => $role], $section + [
                'key' => $role,
                'medias' => [
                    $role => collect($settingsFields['medias'][$role])->map(function ($media) {
                        return json_decode($media, true);
                    })->values()->filter()->toArray(),
                ],
            ]);
        }
    }

    public function getCrops($role)
    {
        return config('twill.settings.crops')[$role];
    }

}
