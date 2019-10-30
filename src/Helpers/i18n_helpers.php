<?php

use Illuminate\Support\Collection;

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
            $allLanguages = Collection::make(config('translatable.locales'))->map(function ($locale, $index) use ($form_fields) {
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
        if (class_exists(Locale::class)) {
            return Locale::getDisplayLanguage($code);
        }

        $codeToLanguageMappings = [
            'ab' => 'Abkhazian',
            'aa' => 'Afar',
            'af' => 'Afrikaans',
            'sq' => 'Albanian',
            'am' => 'Amharic',
            'ar' => 'Arabic',
            'an' => 'Aragonese',
            'hy' => 'Armenian',
            'as' => 'Assamese',
            'ay' => 'Aymara',
            'az' => 'Azerbaijani',
            'ba' => 'Bashkir',
            'eu' => 'Basque',
            'bn' => 'Bengali (Bangla)',
            'dz' => 'Bhutani',
            'bh' => 'Bihari',
            'bi' => 'Bislama',
            'br' => 'Breton',
            'bg' => 'Bulgarian',
            'my' => 'Burmese',
            'be' => 'Byelorussian (Belarusian)',
            'km' => 'Cambodian',
            'ca' => 'Catalan',
            'zh' => 'Chinese',
            'zh-Hans' => 'Chinese (simplified)',
            'zh-Hant' => 'Chinese (traditional)',
            'co' => 'Corsican',
            'hr' => 'Croatian',
            'cs' => 'Czech',
            'da' => 'Danish',
            'nl' => 'Dutch',
            'en' => 'English',
            'eo' => 'Esperanto',
            'et' => 'Estonian',
            'fo' => 'Faeroese',
            'fa' => 'Farsi',
            'fj' => 'Fiji',
            'fi' => 'Finnish',
            'fr' => 'French',
            'fy' => 'Frisian',
            'gd' => 'Gaelic (Scottish)',
            'gv' => 'Gaelic (Manx)',
            'gl' => 'Galician',
            'ka' => 'Georgian',
            'de' => 'German',
            'el' => 'Greek',
            'kl' => 'Kalaallisut (Greenlandic)',
            'gn' => 'Guarani',
            'gu' => 'Gujarati',
            'ht' => 'Haitian Creole',
            'ha' => 'Hausa',
            'he' => 'Hebrew',
            'iw' => 'Hebrew',
            'hi' => 'Hindi',
            'hu' => 'Hungarian',
            'is' => 'Icelandic',
            'io' => 'Ido',
            'id' => 'Indonesian',
            'in' => 'Indonesian',
            'ia' => 'Interlingua',
            'ie' => 'Interlingue',
            'iu' => 'Inuktitut',
            'ik' => 'Inupiak',
            'ga' => 'Irish',
            'it' => 'Italian',
            'ja' => 'Japanese',
            'jv' => 'Javanese',
            'kn' => 'Kannada',
            'ks' => 'Kashmiri',
            'kk' => 'Kazakh',
            'rw' => 'Kinyarwanda (Ruanda)',
            'ky' => 'Kirghiz',
            'rn' => 'Kirundi (Rundi)',
            'ko' => 'Korean',
            'ku' => 'Kurdish',
            'lo' => 'Laothian',
            'la' => 'Latin',
            'lv' => 'Latvian (Lettish)',
            'li' => 'Limburgish ( Limburger)',
            'ln' => 'Lingala',
            'lt' => 'Lithuanian',
            'mk' => 'Macedonian',
            'mg' => 'Malagasy',
            'ms' => 'Malay',
            'ml' => 'Malayalam',
            'mt' => 'Maltese',
            'mi' => 'Maori',
            'mr' => 'Marathi',
            'mo' => 'Moldavian',
            'mn' => 'Mongolian',
            'na' => 'Nauru',
            'ne' => 'Nepali',
            'no' => 'Norwegian',
            'oc' => 'Occitan',
            'or' => 'Oriya',
            'om' => 'Oromo (Afan, Galla)',
            'ps' => 'Pashto (Pushto)',
            'pl' => 'Polish',
            'pt' => 'Portuguese',
            'pa' => 'Punjabi',
            'qu' => 'Quechua',
            'rm' => 'Rhaeto-Romance',
            'ro' => 'Romanian',
            'ru' => 'Russian',
            'sm' => 'Samoan',
            'sg' => 'Sango',
            'sa' => 'Sanskrit',
            'sr' => 'Serbian',
            'sh' => 'Serbo-Croatian',
            'st' => 'Sesotho',
            'tn' => 'Setswana',
            'sn' => 'Shona',
            'ii' => 'Sichuan Yi',
            'sd' => 'Sindhi',
            'si' => 'Sinhalese',
            'ss' => 'Siswati',
            'sk' => 'Slovak',
            'sl' => 'Slovenian',
            'so' => 'Somali',
            'es' => 'Spanish',
            'su' => 'Sundanese',
            'sw' => 'Swahili (Kiswahili)',
            'sv' => 'Swedish',
            'tl' => 'Tagalog',
            'tg' => 'Tajik',
            'ta' => 'Tamil',
            'tt' => 'Tatar',
            'te' => 'Telugu',
            'th' => 'Thai',
            'bo' => 'Tibetan',
            'ti' => 'Tigrinya',
            'to' => 'Tonga',
            'ts' => 'Tsonga',
            'tr' => 'Turkish',
            'tk' => 'Turkmen',
            'tw' => 'Twi',
            'ug' => 'Uighur',
            'uk' => 'Ukrainian',
            'ur' => 'Urdu',
            'uz' => 'Uzbek',
            'vi' => 'Vietnamese',
            'vo' => 'Volapük',
            'wa' => 'Wallon',
            'cy' => 'Welsh',
            'wo' => 'Wolof',
            'xh' => 'Xhosa',
            'yi' => 'Yiddish',
            'ji' => 'Yiddish',
            'yo' => 'Yoruba',
            'zu' => 'Zulu',
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
        $words = join(" ", $a);
        return ucfirst(strtolower($words));
    }
}

if (!function_exists('getLanguageNativeNameFromCode')) {
    /**
     * @param string $code
     * @return string
     */
    function getLanguageNativeNameFromCode($code)
    {
        $codeToLanguageMappings = [
            'ar' => 
                [
                    'english_name' => 'Arabic',
                    'native_name' => 'العربية',
                ],
            'bg' => 
                [
                    'english_name' => 'Bulgarian',
                    'native_name' => 'български език',
                ],
            'zh-CN' => 
                [
                    'english_name' => 'Chinese (Simplified)',
                    'native_name' => '简体中文',
                ],
            'zh-TW' => 
                [
                    'english_name' => 'Chinese (Traditional)',
                    'native_name' => '繁體中文',
                ],
            'cs' => 
                [
                    'english_name' => 'Czech',
                    'native_name' => 'čeština',
                ],
            'da' => 
                [
                    'english_name' => 'Danish',
                    'native_name' => 'Dansk',
                ],
            'nl' => 
                [
                    'english_name' => 'Dutch',
                    'native_name' => 'Nederlands',
                ],
            'en' => 
                [
                    'english_name' => 'English',
                    'native_name' => 'English',
                ],
            'fi' => 
                [
                    'english_name' => 'Finnish',
                    'native_name' => 'Suomi',
                ],
            'fr' => 
                [
                    'english_name' => 'French',
                    'native_name' => 'Français',
                ],
            'de' => 
                [
                    'english_name' => 'German',
                    'native_name' => 'Deutsch',
                ],
            'el' => 
                [
                    'english_name' => 'Greek',
                    'native_name' => 'Ελληνικά',
                ],
            'hu' => 
                [
                    'english_name' => 'Hungarian',
                    'native_name' => 'Magyar',
                ],
            'it' => 
                [
                    'english_name' => 'Italian',
                    'native_name' => 'Italiano',
                ],
            'ja' => 
                [
                    'english_name' => 'Japanese',
                    'native_name' => '日本語',
                ],
            'ko' => 
                [
                    'english_name' => 'Korean',
                    'native_name' => '한국어',
                ],
            'no' => 
                [
                    'english_name' => 'Norwegian',
                    'native_name' => 'Norsk',
                ],
            'pl' => 
                [
                    'english_name' => 'Polish',
                    'native_name' => 'Polski',
                ],
            'pt' => 
                [
                    'english_name' => 'Portuguese',
                    'native_name' => 'Português',
                ],
            'pt-BR' => 
                [
                    'english_name' => 'Portuguese-Brazil',
                    'native_name' => 'Português-Brasil',
                ],
            'ro' => 
                [
                    'english_name' => 'Romanian',
                    'native_name' => 'Română',
                ],
            'ru' => 
                [
                    'english_name' => 'Russian',
                    'native_name' => 'Русский',
                ],
            'es' => 
                [
                    'english_name' => 'Spanish-Spain',
                    'native_name' => 'Español-España',
                ],
            'es-419' => 
                [
                    'english_name' => 'Spanish-Latin America',
                    'native_name' => 'Español-Latinoamérica',
                ],
            'sv' => 
                [
                    'english_name' => 'Swedish',
                    'native_name' => 'Svenska',
                ],
            'th' => 
                [
                    'english_name' => 'Thai',
                    'native_name' => 'ไทย',
                ],
            'tr' => 
                [
                    'english_name' => 'Turkish',
                    'native_name' => 'Türkçe',
                ],
            'uk' => 
                [
                    'english_name' => 'Ukrainian',
                    'native_name' => 'Українська',
                ],
            'vn' => 
                [
                    'english_name' => 'Vietnamese',
                    'native_name' => 'Tiếng Việt',
                ],
        ];

        return $codeToLanguageMappings[$code]['native_name'] ?? $code ;
    }
}
