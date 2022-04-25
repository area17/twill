<?php

use A17\Twill\Exceptions\NoCapsuleFoundException;
use A17\Twill\Facades\TwillCapsules;
use Illuminate\Support\Facades\Cache;
use A17\Twill\Services\Assets\Twill as TwillAssets;

if (!function_exists('revAsset')) {
    function revAsset(string $file): string
    {
        if (!app()->environment('local', 'development')) {
            try {
                $manifest = Cache::rememberForever('rev-manifest', function () {
                    return json_decode(file_get_contents(config('twill.frontend.rev_manifest_path')), true);
                });

                if (isset($manifest[$file])) {
                    return (rtrim(config('twill.frontend.dist_assets_path'), '/') . '/') . $manifest[$file];
                }
            } catch (Exception) {
                return '/' . $file;
            }
        }

        return (rtrim(config('twill.frontend.dev_assets_path'), '/') . '/') . $file;
    }
}

if (!function_exists('twillAsset')) {
    function twillAsset(string $file): string
    {
        return app(TwillAssets::class)->asset($file);
    }
}

if (!function_exists('icon')) {
    /**
     * ARIA roles memo: 'presentation' means merely decoration. Otherwise, use role="img".
     */
    function icon(string $name, array $opts = []): string
    {
        $title = isset($opts['title']) ? ' title="' . htmlentities($opts['title'], ENT_QUOTES, 'UTF-8') . '" ' : '';
        $role = isset($opts['role']) ? ' role="' . htmlentities(
                $opts['role'],
                ENT_QUOTES,
                'UTF-8'
            ) . '" ' : ' role="presentation" ';
        $css_class = isset($opts['css_class']) ? htmlentities($opts['css_class'], ENT_QUOTES, 'UTF-8') : '';
        $svg_link = config('twill.frontend.svg_sprites_use_hash_only') ? sprintf('#icon--%s', $name) : revAsset(
                config('twill.frontend.svg_sprites_path')
            ) . sprintf('#icon--%s', $name);
        return sprintf('<svg class="icon--%s %s" %s %s><use xlink:href="', $name, $css_class, $title, $role) . $svg_link . '"></use></svg>';
    }
}

if (!function_exists('twillViewName')) {
    function twillViewName($module, $suffix)
    {
        $view = sprintf('twill.%s.%s', $module, $suffix);

        if (view()->exists($view)) {
            return $view;
        }

        // No module is set.
        if (!$module) {
            return sprintf('.%s', $suffix);
        }

        try {
            $prefix = TwillCapsules::getCapsuleForModule($module)->getViewPrefix();
        } catch (NoCapsuleFoundException) {
            $prefix = null;
        }

        return sprintf('%s.%s', $prefix, $suffix);
    }
}
