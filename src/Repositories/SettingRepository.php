<?php

namespace A17\Twill\Repositories;

use A17\Twill\Models\Setting;

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
            $settingValue = [];
            foreach ($setting->translations as $translation) {
                $settingValue[$translation->locale] = $translation->value;
            }

            return [$setting->key => $settingValue];
        });
    }

    public function update($settingsFields, $section = null)
    {
        $section = $section ? ['section' => $section] : [];

        foreach ($settingsFields as $key => $value) {
            // field translation is disabled
            if (is_string($value)) {
                array_set($settings, $key, ['key' => $key] + $section + ['value' => $value]);
            } else {
                foreach (getLocales() as $locale) {
                    array_set(
                        $settingsTranslated,
                        $key . '.' . $locale,
                        ['value' => $value[$locale]] + ['active' => true]
                    );
                }
                foreach ($settingsTranslated as $key => $values) {
                    array_set($settings, $key, ['key' => $key] + $section + $values);
                }
            }
        }

        foreach ($settings as $key => $setting) {
            $this->model->updateOrCreate(['key' => $key] + $section, $setting);
        }
    }
}
