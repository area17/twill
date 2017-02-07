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
