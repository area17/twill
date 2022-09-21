<?php

use A17\Twill\Helpers\TwillTransString;
use Illuminate\Support\Collection;

if (! function_exists('twillTrans')) {
    function twillTrans($key, $replace = []): TwillTransString
    {
        return new TwillTransString($key, $replace);
    }
}

if (! function_exists('getLocales')) {
    /**
     * @return string[]
     */
    function getLocales()
    {
        $locales = collect(config('translatable.locales'))->map(function ($locale, $index) {
            return collect($locale)->map(function ($country) use ($locale, $index) {
                return is_numeric($index)
                    ? $locale
                    : "$index-$country";
            });
        })->flatten()->toArray();

        if (blank($locales)) {
            $locales = [config('app.locale')];
        }

        return $locales;
    }
}

if (! function_exists('getLanguagesForVueStore')) {
    /**
     * @param array $form_fields
     * @param bool $translate
     * @return array
     */
    function getLanguagesForVueStore($form_fields = [], $translate = true)
    {
        $manageMultipleLanguages = count(getLocales()) > 1;
        if ($manageMultipleLanguages && $translate) {
            $allLanguages = Collection::make(getLocales())->map(function ($locale, $index) use ($form_fields) {
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

if (! function_exists('getLanguageLabelFromLocaleCode')) {
    /**
     * @param string $code
     * @return string
     */
    function getLanguageLabelFromLocaleCode($code, $native = false)
    {
        if (class_exists(Locale::class)) {
            if ($native) {
                return Locale::getDisplayName($code, $code);
            }

            return Locale::getDisplayName($code, config('twill.locale', config('twill.fallback_locale', 'en')));
        }

        $codeToLanguageMappings = getCodeToLanguageMappings();

        if (isset($codeToLanguageMappings[$code])) {
            $lang = $codeToLanguageMappings[$code];
            if (is_array($lang) && isset($lang[1]) && $native) {
                return $lang[1];
            }

            if (is_array($lang) && isset($lang[0])) {
                return $lang[0];
            }

            return $lang;
        }

        return $code;
    }
}

/*
 * Converts camelCase string to have spaces between each.
 * @param string $camelCaseString
 * @return string (ex.: camel case string)
 */
if (! function_exists('camelCaseToWords')) {
    function camelCaseToWords($camelCaseString)
    {
        $re = '/(?<=[a-z])(?=[A-Z])/x';
        $a = preg_split($re, $camelCaseString);
        $words = implode(' ', $a);

        return ucfirst(strtolower($words));
    }
}

if (! function_exists('getCodeToLanguageMappings')) {
    function getCodeToLanguageMappings()
    {
        return [
            'ab' => 'Abkhazian',
            'aa' => 'Afar',
            'af' => 'Afrikaans',
            'sq' => 'Albanian',
            'am' => 'Amharic',
            'ar' => ['Arabic', 'العربية'],
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
            'bg' => ['Bulgarian', 'български език'],
            'my' => 'Burmese',
            'be' => 'Byelorussian (Belarusian)',
            'km' => 'Cambodian',
            'ca' => 'Catalan',
            'zh' => 'Chinese',
            'zh-Hans' => ['Chinese (simplified)', '简体中文'],
            'zh-Hant' => ['Chinese (traditional)', '繁體中文'],
            'co' => 'Corsican',
            'hr' => 'Croatian',
            'cs' => ['Czech', 'čeština'],
            'da' => ['Danish', 'Dansk'],
            'nl' => ['Dutch', 'Nederlands'],
            'en' => 'English',
            'eo' => 'Esperanto',
            'et' => 'Estonian',
            'fo' => 'Faeroese',
            'fa' => 'Farsi',
            'fj' => ['Fiji', 'Suomi'],
            'fi' => 'Finnish',
            'fr' => ['French', 'Français'],
            'fy' => 'Frisian',
            'gd' => 'Gaelic (Scottish)',
            'gv' => 'Gaelic (Manx)',
            'gl' => 'Galician',
            'ka' => 'Georgian',
            'de' => ['German', 'Deutsch'],
            'el' => ['Greek', 'Ελληνικά'],
            'kl' => 'Kalaallisut (Greenlandic)',
            'gn' => 'Guarani',
            'gu' => 'Gujarati',
            'ht' => 'Haitian Creole',
            'ha' => 'Hausa',
            'he' => 'Hebrew',
            'iw' => 'Hebrew',
            'hi' => 'Hindi',
            'hu' => ['Hungarian', 'Magyar'],
            'is' => 'Icelandic',
            'io' => 'Ido',
            'id' => 'Indonesian',
            'in' => 'Indonesian',
            'ia' => 'Interlingua',
            'ie' => 'Interlingue',
            'iu' => 'Inuktitut',
            'ik' => 'Inupiak',
            'ga' => 'Irish',
            'it' => ['Italian', 'Italiano'],
            'ja' => ['Japanese', '日本語'],
            'jv' => 'Javanese',
            'kn' => 'Kannada',
            'ks' => 'Kashmiri',
            'kk' => 'Kazakh',
            'rw' => 'Kinyarwanda (Ruanda)',
            'ky' => 'Kirghiz',
            'rn' => 'Kirundi (Rundi)',
            'ko' => ['Korean', '한국어'],
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
            'pl' => ['Polish', 'Polski'],
            'pt' => ['Portuguese', 'Português'],
            'pa' => 'Punjabi',
            'qu' => 'Quechua',
            'rm' => 'Rhaeto-Romance',
            'ro' => ['Romanian', 'Română'],
            'ru' => ['Russian', 'Русский'],
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
            'es' => ['Spanish', 'Español'],
            'su' => 'Sundanese',
            'sw' => 'Swahili (Kiswahili)',
            'sv' => ['Swedish', 'Svenska'],
            'tl' => 'Tagalog',
            'tg' => 'Tajik',
            'ta' => 'Tamil',
            'tt' => 'Tatar',
            'te' => 'Telugu',
            'th' => ['Thai', 'ไทย'],
            'bo' => 'Tibetan',
            'ti' => 'Tigrinya',
            'to' => 'Tonga',
            'ts' => 'Tsonga',
            'tr' => ['Turkish', 'Türkçe'],
            'tk' => 'Turkmen',
            'tw' => 'Twi',
            'ug' => 'Uighur',
            'uk' => ['Ukrainian', 'Українська'],
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
    }
}
