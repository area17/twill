<?php

namespace A17\CmsToolkit\Repositories;

use A17\CmsToolkit\Models\Setting;

class SettingRepository
{

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
        return $this->model->when($section, function ($query) use ($section) {
            $query->where('section', $section);
        })->with('translations')->get()->mapWithKeys(function ($setting) {
            if ($setting->translations->count() >= 1) {
                return $setting->translations->mapWithKeys(function ($translation) use ($setting) {
                    return [$setting->key . '_' . $translation->locale => $translation->value];
                });
            }

            return [$setting->key => $setting->translations->first()->value];
        });
    }

    public function update($settingsFields, $section = null)
    {
        $section = $section ? ['section' => $section] : [];

        $settingsTranslatedDotted = collect($settingsFields)->mapWithKeys(function ($value, $key) {
            return [(ends_with($key, getLocales()) ? str_replace_last('_', '.', $key) : $key) => $value];
        });

        foreach ($settingsTranslatedDotted as $key => $value) {
            array_set($settingsTranslated, $key, ['value' => $value] + ['active' => true]);
        }

        foreach ($settingsTranslated as $key => $values) {
            array_set($settings, $key, ['key' => $key] + $section + $values);
        }

        foreach ($settings as $key => $setting) {
            $this->model->updateOrCreate(['key' => $key] + $section, $setting);
        }
    }
}
