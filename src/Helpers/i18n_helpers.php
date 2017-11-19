<?php

if (!function_exists('getLocales')) {
    function getLocales()
    {
        return config('translatable.locales');
    }
}

if (!function_exists('getFallbackLocale')) {
    function getFallbackLocale()
    {
        return config('translatable.fallback_locale');
    }
}

/**
 * Converts camelCase string to have spaces between each.
 * @param $camelCaseString
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
