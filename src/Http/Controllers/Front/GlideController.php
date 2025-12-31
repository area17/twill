<?php

namespace A17\Twill\Http\Controllers\Front;

use A17\Twill\Services\MediaLibrary\Glide;
use Illuminate\Config\Repository as Config;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\Glide\Filesystem\FileNotFoundException;

class GlideController
{
    public function __invoke($path, Application $app, Config $config)
    {
        if (
            $config->get('twill.glide.use_streamed_response_for_original_media', false) &&
            Str::endsWith($path, $config->get('twill.glide.original_media_for_extensions'))
        ) {
            $disk = $config->get('twill.glide.use_source_disk')
                ? $config->get('twill.glide.source_disk')
                : $config->get('twill.media_library.disk');

            return Storage::disk($disk)->response($path);
        }

        try {
            return $app->make(Glide::class)->render($path);
        } catch (FileNotFoundException) {
            abort(404);
        }
    }
}
