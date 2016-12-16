<?php

if (!function_exists('getLocales')) {
    function getLocales()
    {
        return config('translatable.locales');
    }
}
