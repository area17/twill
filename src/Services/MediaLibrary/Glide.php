<?php

namespace A17\Twill\Services\MediaLibrary;

use Illuminate\Config\Repository as Config;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use League\Glide\Responses\LaravelResponseFactory;
use League\Glide\ServerFactory;
use League\Glide\Signatures\SignatureFactory;
use League\Glide\Urls\UrlBuilderFactory;

class Glide implements ImageServiceInterface
{
    use ImageServiceDefaults;

    private \League\Glide\Server $server;

    private \League\Glide\Urls\UrlBuilder $urlBuilder;

    public function __construct(protected Config $config, protected Application $app, protected Request $request)
    {
        $baseUrlHost = $this->config->get(
            'twill.glide.base_url',
            $this->request->getScheme() . '://' . str_replace(
                ['http://', 'https://'],
                '',
                $this->config->get('app.url')
            )
        );

        $baseUrl = implode('/', [
            rtrim($baseUrlHost, '/'),
            ltrim($this->config->get('twill.glide.base_path'), '/'),
        ]);

        if (!Str::startsWith($baseUrl, ['http://', 'https://'])) {
            $baseUrl = $this->request->getScheme() . '://' . $baseUrl;
        }

        $this->server = ServerFactory::create([
            'response' => new LaravelResponseFactory($this->request),
            'source' => $this->config->get('twill.glide.source'),
            'cache' => $this->config->get('twill.glide.cache'),
            'cache_path_prefix' => $this->config->get('twill.glide.cache_path_prefix'),
            'base_url' => $baseUrl,
            'presets' => $this->config->get('twill.glide.presets', []),
            'driver' => $this->config->get('twill.glide.driver')
        ]);

        $this->urlBuilder = UrlBuilderFactory::create(
            $baseUrl,
            $this->config->get('twill.glide.use_signed_urls') ? $this->config->get('twill.glide.sign_key') : null
        );
    }

    /**
     * @return mixed
     */
    public function render(string $path)
    {
        if ($this->config->get('twill.glide.use_signed_urls')) {
            SignatureFactory::create($this->config->get('twill.glide.sign_key'))->validateRequest($this->config->get('twill.glide.base_path') . '/' . $path, $this->request->all());
        }

        return $this->server->getImageResponse($path, $this->request->all());
    }

    /**
     * @param string $id
     */
    public function getUrl($id, array $params = []): string
    {
        $defaultParams = config('twill.glide.default_params');

        return $this->getOriginalMediaUrl($id) ??
            $this->urlBuilder->getUrl($id, array_replace($defaultParams, $params));
    }

    /**
     * @param string $id
     */
    public function getUrlWithCrop($id, array $cropParams, array $params = []): string
    {
        return $this->getUrl($id, $this->getCrop($cropParams) + $params);
    }

    /**
     * @param string $id
     * @param mixed $width
     * @param mixed $height
     */
    public function getUrlWithFocalCrop($id, array $cropParams, $width, $height, array $params = []): string
    {
        return $this->getUrl($id, $this->getFocalPointCrop($cropParams, $width, $height) + $params);
    }

    /**
     * @param string $id
     */
    public function getLQIPUrl($id, array $params = []): string
    {
        $defaultParams = config('twill.glide.lqip_default_params');

        $cropParams = Arr::has($params, $this->cropParamsKeys) ? $this->getCrop($params) : [];

        $params = Arr::except($params, $this->cropParamsKeys);

        return $this->getUrl($id, array_replace($defaultParams, $params + $cropParams));
    }

    /**
     * @param string $id
     */
    public function getSocialUrl($id, array $params = []): string
    {
        $defaultParams = config('twill.glide.social_default_params');

        $cropParams = Arr::has($params, $this->cropParamsKeys) ? $this->getCrop($params) : [];

        $params = Arr::except($params, $this->cropParamsKeys);

        return $this->getUrl($id, array_replace($defaultParams, $params + $cropParams));
    }

    /**
     * @param string $id
     */
    public function getCmsUrl($id, array $params = []): string
    {
        $defaultParams = config('twill.glide.cms_default_params');

        $cropParams = Arr::has($params, $this->cropParamsKeys) ? $this->getCrop($params) : [];

        $params = Arr::except($params, $this->cropParamsKeys);

        return $this->getUrl($id, array_replace($defaultParams, $params + $cropParams));
    }

    /**
     * @param string $id, string $preset
     */
    public function getPresetUrl(string $id, $preset): string
    {
        return $this->getRawUrl($id) . '?p=' . $preset;
    }

    /**
     * @param string $id
     */
    public function getRawUrl($id): string
    {
        return $this->getOriginalMediaUrl($id) ?? $this->urlBuilder->getUrl($id);
    }

    /**
     * @param string $id
     * @return array<string, int>|array<string, mixed>
     */
    public function getDimensions($id): array
    {
        $url = $this->urlBuilder->getUrl($id);

        try {
            list($w, $h) = getimagesize($url);

            return [
                'width' => $w,
                'height' => $h,
            ];
        } catch (\Exception) {
            return [
                'width' => 0,
                'height' => 0,
            ];
        }
    }

    /**
     * @param mixed[] $crop_params
     * @return mixed[]
     */
    protected function getCrop(array $crop_params): array
    {
        if (!empty($crop_params)) {
            return ['crop' =>
                $crop_params['crop_w'] . ',' .
                $crop_params['crop_h'] . ',' .
                $crop_params['crop_x'] . ',' .
                $crop_params['crop_y'],
            ];
        }

        return [];
    }

    /**
     * @param mixed[] $crop_params
     * @return mixed[]
     */
    protected function getFocalPointCrop(array $crop_params, int $width, int $height): array
    {
        if (!empty($crop_params)) {
            // determine center coordinates of user crop and express it in term of original image width and height percentage
            $fpX = 100 * ($crop_params['crop_w'] / 2 + $crop_params['crop_x']) / $width;
            $fpY = 100 * ($crop_params['crop_h'] / 2 + $crop_params['crop_y']) / $height;

            // determine focal zoom
            if ($crop_params['crop_w'] > $crop_params['crop_h']) {
                $fpZ = $width / ($crop_params['crop_w'] ?? $width);
            } else {
                $fpZ = $height / ($crop_params['crop_h'] ?? $height);
            }

            $fpX = number_format($fpX, 0, ".", "");
            $fpY = number_format($fpY, 0, ".", "");
            $fpZ = number_format($fpZ, 4, ".", "");

            return ['fit' => 'crop-' . $fpX . '-' . $fpY . '-' . $fpZ];
        }

        return [];
    }

    /**
     * @return string
     */
    private function getOriginalMediaUrl(string $id)
    {
        $originalMediaForExtensions = $this->config->get('twill.glide.original_media_for_extensions');
        $addParamsToSvgs = $this->config->get('twill.glide.add_params_to_svgs', false);

        if ((Str::endsWith($id, '.svg') && $addParamsToSvgs) || !Str::endsWith($id, $originalMediaForExtensions)) {
            return null;
        }

        return Storage::disk(config('twill.media_library.disk'))->url($id);
    }
}
