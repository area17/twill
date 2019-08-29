<?php

namespace A17\Twill\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;

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
     * Gets the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch ($this->method()) {
            case 'POST':{return $this->rulesForCreate();}
            case 'PUT':{return $this->rulesForUpdate();}
            default:break;
        }

        return [];
    }

    /**
     * Gets the validation rules that apply to the translated fields.
     *
     * @return array
     */
    protected function rulesForTranslatedFields($rules, $fields)
    {
        $locales = getLocales();
        $localeActive = false;
        foreach ($locales as $locale) {
            if ($this->request->has('languages')) {
                $languageFromRequest = Collection::make($this->request->get('languages'))->where('value', $locale)->first();
                if ($languageFromRequest['published']) {
                    $localeActive = true;
                    $rules = $this->updateRules($rules, $fields, $locale);
                }
            }
        }

        if (!$localeActive) {
            $rules = $this->updateRules($rules, $fields, reset($locales));
        }

        return $rules;
    }

    /**
     * @param array $rules
     * @param array $fields
     * @param string $locale
     * @return array
     */
    private function updateRules($rules, $fields, $locale)
    {
        foreach ($fields as $field => $field_rules) {
            // allows using validation rule that references other fields even for translated fields
            if (str_contains($field_rules, $fields)) {
                foreach ($fields as $fieldName => $fieldRules) {
                    if (str_contains($field_rules, $fieldName) && starts_with('required_', $field_rules)) {
                        $field_rules = str_replace($fieldName, "{$fieldName}.{$locale}", $field_rules);
                    }
                }
            }

            $rules["{$field}.{$locale}"] = $field_rules;
        }

        return $rules;
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
