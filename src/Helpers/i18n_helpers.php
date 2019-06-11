<?php

if (!function_exists('getLocales')) {
    /**
     * @return string[]
     */
    function getLocales()
    {
        return config('translatable.locales') ?? [config('app.locale')];
    }
}

if (!function_exists('getLanguagesForVueStore')) {
    /**
     * @param array $form_fields
     * @param bool $translate
     * @return array
     */
    function getLanguagesForVueStore($form_fields = [], $translate = true)
    {
        $manageMultipleLanguages = count(getLocales()) > 1;
        if ($manageMultipleLanguages && $translate) {
            $allLanguages = collect(config('translatable.locales'))->map(function ($locale, $index) use ($form_fields) {
                return [
                    'shortlabel' => strtoupper($locale),
                    'label' => getLanguageLabelFromLocaleCode($locale),
                    'value' => $locale,
                    'disabled' => false,
                    'published' => $form_fields['translations']['active'][$locale] ?? ($index === 0),
                ];
            });

            return [
                'all' => $allLanguages,
                'active' => request()->has('lang') ? $allLanguages->where('value', request('lang'))->first() : null,
            ];
        }

        $locale = config('app.locale');

        return [
            'all' => [
                [
                    'shortlabel' => strtoupper($locale),
                    'label' => getLanguageLabelFromLocaleCode($locale),
                    'value' => $locale,
                    'disabled' => false,
                    'published' => true,
                ],
            ],
        ];
    }
}

if (!function_exists('getLanguageLabelFromLocaleCode')) {
    /**
     * @param string $code
     * @return string
     */
    function getLanguageLabelFromLocaleCode($code)
    {
        $codeToLanguageMappings = [
            'ar' => 'Arabic',
            'zh' => 'Chinese',
            'zh-Hans' => 'Chinese (simplified)',
            'zh-Hant' => 'Chinese (traditional)',
            'nl' => 'Dutch',
            'en' => 'English',
            'fr' => 'French',
            'de' => 'German',
            'it' => 'Italian',
            'ja' => 'Japanese',
            'ru' => 'Russian',
            'es' => 'Spanish',
            'pt' => 'Portuguese',
        ];

        return $codeToLanguageMappings[$code] ?? $code;
    }
}

/**
 * Converts camelCase string to have spaces between each.
 * @param string $camelCaseString
 * @return string (ex.: camel case string)
 */
if (!function_exists('camelCaseToWords')) {
    function camelCaseToWords($camelCaseString)
    {
        $re = '/(?<=[a-z])(?=[A-Z])/x';
        $a = preg_split($re, $camelCaseString);
        $words = join($a, " ");
        return ucfirst(strtolower($words));
    }
}
