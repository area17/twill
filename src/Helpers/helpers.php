<?php

// adding this to have a better debug display in Chrome dev tools when
// dd'ing during AJAX requests (see Symfony dumper issue in Chrome > 60:
// https://github.com/symfony/symfony/issues/24688)
if (!function_exists('ddd')) {
    function ddd(...$args)
    {
        http_response_code(500);
        call_user_func_array('dd', $args);
    }
}

if (!function_exists('classUsesDeep')) {
    function classUsesDeep($class, $autoload = true)
    {
        $traits = [];

        // Get traits of all parent classes
        do {
            $traits = array_merge(class_uses($class, $autoload), $traits);
        } while ($class = get_parent_class($class));

        // Get traits of all parent traits
        $traitsToSearch = $traits;
        while (!empty($traitsToSearch)) {
            $newTraits = class_uses(array_pop($traitsToSearch), $autoload);
            $traits = array_merge($newTraits, $traits);
            $traitsToSearch = array_merge($newTraits, $traitsToSearch);
        }

        foreach ($traits as $trait => $same) {
            $traits = array_merge(class_uses($trait, $autoload), $traits);
        }

        return array_unique($traits);
    }
}

if (!function_exists('classHasTrait')) {
    function classHasTrait($class, $trait)
    {
        $traits = classUsesDeep($class);

        if (in_array($trait, array_keys($traits))) {
            return true;
        }

        return false;
    }
}

if (!function_exists('getFormFieldsValue')) {
    function getFormFieldsValue($formFields, $name)
    {
        return array_get($formFields, str_replace(']', '', str_replace('[', '.', $name)), '');
    }
}

if (!function_exists('fireCmsEvent')) {
    function fireCmsEvent($eventName, $input = [])
    {
        $method = method_exists(\Illuminate\Events\Dispatcher::class, 'dispatch') ? 'dispatch' : 'fire';
        Event::$method($eventName, [$eventName, $input]);
    }
}

if (!function_exists('twill_path')) {
    function twill_path($path = '')
    {
        // Split to separate root namespace
        preg_match('/(\w*)\W?(.*)/', config('twill.namespace'), $matches);

        // Namespace App is unchanged in config?
        if ($matches[0] === 'App') {
            return app_path($path);
        }

        // If it it still starts with App, use the left part, otherwise use the whole namespace
        // This can be a problem for those using a completely different app path for the application
        $left = ($matches[1] === 'App' ? $matches[2] : $matches[0]);

        // Join, fix slashes for the current operating system, and return path
        return app_path(str_replace(
            '\\',
            DIRECTORY_SEPARATOR,
            $left . (filled($path) ? '\\' . $path : '')
        ));
    }
}
