<?php

namespace A17\Twill\Jobs;

use A17\Twill\Models\Media;
use A17\Twill\Services\MediaLibrary\Glide;
use A17\Twill\Services\MediaLibrary\ImageService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Config\Repository as Config;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;

class RegenerateMediaLqip implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    protected $model;

    public function __construct(
        $model
    )
    {
        $this->model = $model;
    }

    public function handle(Config $config)
    {
        $this
            ->model
            ->medias
            ->each(function (Media $media) use ($config): void {
                $lqip_width = $config->get('lqip.' . $media->pivot->mediable_type . '.' . $media->pivot->role . '.' . $media->pivot->crop, 30);

                $crop_params = $media->pivot->only([
                    'crop_x',
                    'crop_y',
                    'crop_w',
                    'crop_h',
                ]);

                $url = ImageService::getLQIPUrl($media->uuid, $crop_params + ['w' => $lqip_width]);

                if (config('twill.media_library.image_service') === Glide::class && !config('twill.glide.base_url')) {
                    throw new Exception('Cannot generate LQIP. Missing glide base url. Please set GLIDE_BASE_URL in your .env');
                }

                $response = Http::get($url);

                if ($response->failed()) {
                    return;
                }

                $dataUri = 'data:image/gif;base64,' . base64_encode($response->body());
                $media->pivot->update(['lqip_data' => $dataUri]);
            });

    }
}
