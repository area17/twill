<?php

namespace A17\Twill\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

abstract class Request extends FormRequest
{
    /**
     * Determines if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * @return array
     */
    public function rulesForCreate()
    {
        return [];
    }

    /**
     * @return array
     */
    public function rulesForUpdate()
    {
        return [];
    }

    /**
     * Gets the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch ($this->method()) {
            case 'POST':
                return $this->rulesForCreate();
            case 'PUT':
                return $this->rulesForUpdate();
            default:
                break;
        }

        return [];
    }

    protected function rulesForTranslatedFields(array $existingRules, array $translatedFieldRules): array
    {
        $locales = getLocales();
        $localeActive = false;

        if ($this->request->has('languages')) {
            foreach ($locales as $locale) {
                $language = Collection::make($this->request->all('languages'))->where('value', $locale)->first();
                $currentLocaleActive = $language['published'] ?? false;
                $existingRules = $this->updateRules($existingRules, $translatedFieldRules, $locale, $currentLocaleActive);

                if ($currentLocaleActive) {
                    $localeActive = true;
                }
            }
        }

        if (! $localeActive) {
            $existingRules = $this->updateRules($existingRules, $translatedFieldRules, reset($locales));
        }

        return $existingRules;
    }

    /**
     * @param array $rules
     * @param array $fields
     * @param string $locale
     * @param bool $localeActive
     * @return array
     */
    private function updateRules($rules, $fields, $locale, $localeActive = true): array
    {
        $fieldNames = array_keys($fields);

        foreach ($fields as $field => $fieldRules) {
            if (is_string($fieldRules)) {
                $fieldRules = explode('|', $fieldRules);
            }

            $fieldRules = collect($fieldRules);

            // Remove required rules and add nullable rules, when locale is not active
            if (! $localeActive) {
                $fieldRules = $fieldRules->reject(fn ($rule) => $this->ruleStartsWith($rule, 'required'));

                if (!$fieldRules->contains(fn ($rule) => $this->ruleStartsWith($rule, 'nullable'))) {
                    $fieldRules->add('nullable');
                }
            }

            $rules["$field.$locale"] = $fieldRules->map(function ($rule) use ($locale, $fieldNames) {
                // allows using validation rule that references other fields even for translated fields
                if ($this->ruleStartsWith($rule, 'required_') && Str::contains($rule, $fieldNames)) {
                    foreach ($fieldNames as $fieldName) {
                        $rule = str_replace($fieldName, "{$fieldName}.{$locale}", $rule);
                    }
                }

                return $rule;
            })->all();
        }

        return $rules;
    }

    /**
     * @param mixed $rule
     * @param string $needle
     *
     * @return bool
     */
    private function ruleStartsWith($rule, $needle)
    {
        return is_string($rule) && Str::startsWith($rule, $needle);
    }

    /**
     * Gets the error messages for the defined validation rules.
     *
     * @param array $messages
     * @param array $fields
     * @return array
     */
    protected function messagesForTranslatedFields($messages, $fields)
    {
        foreach (getLocales() as $locale) {
            $messages = $this->updateMessages($messages, $fields, $locale);
        }

        return $messages;
    }

    /**
     * @param array $messages
     * @param array $fields
     * @param string $locale
     * @return array
     */
    private function updateMessages($messages, $fields, $locale)
    {
        foreach ($fields as $field => $message) {
            $fieldSplitted = explode('.', $field);
            $rule = array_pop($fieldSplitted);
            $field = implode('.', $fieldSplitted);
            $messages["{$field}.{$locale}.$rule"] = str_replace('{lang}', $locale, $message);
        }

        return $messages;
    }
}
