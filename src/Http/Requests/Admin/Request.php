<?php

namespace A17\CmsToolkit\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

abstract class Request extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    protected function rulesForTranslatedFields($rules, $fields)
    {
        $locales = getLocales();
        $localeActive = false;
        foreach ($locales as $locale) {
            if ($this->request->has("active_{$locale}")) {
                $localeActive = true;
                $rules = $this->updateRules($rules, $fields, $locale);
            }
        }

        if (!$localeActive) {
            $rules = $this->updateRules($rules, $fields, reset($locales));
        }

        return $rules;
    }

    private function updateRules($rules, $fields, $locale)
    {
        foreach ($fields as $field => $field_rules) {
            // allows using validation rule that references other fields even for translated fields
            if (str_contains($field_rules, $fields)) {
                foreach ($fields as $fieldName => $fieldRules) {
                    if (str_contains($field_rules, $fieldName) && starts_with('required_', $field_rules)) {
                        $field_rules = str_replace($fieldName, "{$fieldName}_{$locale}", $field_rules);
                    }
                }
            }

            $rules["{$field}_{$locale}"] = $field_rules;
        }

        return $rules;
    }

    protected function messagesForTranslatedFields($messages, $fields)
    {
        foreach (getLocales() as $locale) {
            $messages = $this->updateMessages($messages, $fields, $locale);
        }

        return $messages;
    }

    private function updateMessages($messages, $fields, $locale)
    {
        foreach ($fields as $field => $message) {
            $fieldSplitted = explode('.', $field);
            $rule = array_pop($fieldSplitted);
            $field = implode('.', $fieldSplitted);
            $messages["{$field}_{$locale}.$rule"] = str_replace('{lang}', $locale, $message);
        }

        return $messages;
    }

}
