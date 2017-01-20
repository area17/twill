<?php

if (!function_exists('revAsset')) {
    function revAsset($file)
    {
        if (!app()->environment('local', 'development')) {

            $manifest = Cache::rememberForever('rev-manifest', function () {
                return json_decode(file_get_contents(public_path('dist/rev-manifest.json')), true);
            });

            if (isset($manifest[$file])) {
                return '/dist/' . $manifest[$file];
            }

            throw new InvalidArgumentException("File {$file} not defined in assets manifest.");

        }

        return '/dist/' . $file;
    }
}

if (!function_exists('icon')) {
    /**
     * ARIA roles memo: 'presentation' means merely decoration. Otherwise, use role="img".
     */
    function icon($name, $opts = [])
    {
        $title = isset($opts['title']) ? ' title="' . htmlentities($opts['title'], ENT_QUOTES, 'UTF-8') . '" ' : '';
        $role = isset($opts['role']) ? ' role="' . htmlentities($opts['role'], ENT_QUOTES, 'UTF-8') . '" ' : ' role="presentation" ';
        $css_class = isset($opts['css_class']) ? htmlentities($opts['css_class'], ENT_QUOTES, 'UTF-8') : '';
        return "<svg class=\"icon--$name $css_class\" $title $role><use xlink:href=\"" . revAsset('sprites.svg') . "#icon--$name\"></use></svg>";
    }
}
