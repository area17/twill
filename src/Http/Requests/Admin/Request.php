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

    /**
     * Gets the validation rules that apply to the translated fields.
     *
     * @return array
     */
    protected function rulesForTranslatedFields($rules, $fields)
    {
        $locales = getLocales();
        $localeActive = false;

        if ($this->request->has('languages')) {
            foreach ($locales as $locale) {
                $language = Collection::make($this->request->get('languages'))->where('value', $locale)->first();
                $currentLocaleActive = $language['published'] ?? false;
                $rules = $this->updateRules($rules, $fields, $locale, $currentLocaleActive);

                if ($currentLocaleActive) {
                    $localeActive = true;
                }
            }
        }

        if (! $localeActive) {
            $rules = $this->updateRules($rules, $fields, reset($locales));
        }

        return $rules;
    }

    /**
     * @return array
     * @param mixed[] $rules
     * @param mixed[] $fields
     */
    private function updateRules(array $rules, array $fields, string $locale, bool $localeActive = true)
    {
        $fieldNames = array_keys($fields);

        foreach ($fields as $field => $fieldRules) {
            if (is_string($fieldRules)) {
                $fieldRules = explode('|', $fieldRules);
            }

            $fieldRules = Collection::make($fieldRules);

            // Remove required rules, when locale is not active
            if (! $localeActive) {
                $hasRequiredRule = $fieldRules->contains(function ($rule): bool {
                    return $this->ruleStartsWith($rule, 'required');
                });

                $fieldRules = $fieldRules->reject(function ($rule): bool {
                    return $this->ruleStartsWith($rule, 'required');
                });

                if ($hasRequiredRule && $fieldRules->doesntContain('nullable')) {
                    $fieldRules->add('nullable');
                }
            }

            $rules[sprintf('%s.%s', $field, $locale)] = $fieldRules->map(function ($rule) use ($locale, $fieldNames) {
                // allows using validation rule that references other fields even for translated fields
                if ($this->ruleStartsWith($rule, 'required_') && Str::contains($rule, $fieldNames)) {
                    foreach ($fieldNames as $fieldName) {
                        $rule = str_replace($fieldName, sprintf('%s.%s', $fieldName, $locale), $rule);
                    }
                }

                return $rule;
            })->toArray();
        }

        return $rules;
    }

    /**
     * @param mixed $rule
     *
     * @return bool
     */
    private function ruleStartsWith($rule, string $needle)
    {
        return is_string($rule) && Str::startsWith($rule, $needle);
    }

    /**
     * Gets the error messages for the defined validation rules.
     *
     * @return array
     * @param mixed[] $messages
     * @param mixed[] $fields
     */
    protected function messagesForTranslatedFields(array $messages, array $fields)
    {
        foreach (getLocales() as $locale) {
            $messages = $this->updateMessages($messages, $fields, $locale);
        }

        return $messages;
    }

    /**
     * @return array
     * @param mixed[] $messages
     * @param mixed[] $fields
     */
    private function updateMessages(array $messages, array $fields, string $locale)
    {
        foreach ($fields as $field => $message) {
            $fieldSplitted = explode('.', $field);
            $rule = array_pop($fieldSplitted);
            $field = implode('.', $fieldSplitted);
            $messages[sprintf('%s.%s.%s', $field, $locale, $rule)] = str_replace('{lang}', $locale, $message);
        }

        return $messages;
    }
}
