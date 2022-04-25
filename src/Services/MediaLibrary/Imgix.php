<?php

namespace A17\Twill\Services\MediaLibrary;

use Illuminate\Config\Repository as Config;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Imgix\UrlBuilder;

class Imgix implements ImageServiceInterface
{
    use ImageServiceDefaults;

    private \Imgix\UrlBuilder $urlBuilder;

    public function __construct(protected Config $config)
    {
        $urlBuilder = new UrlBuilder(
            $this->config->get('twill.imgix.source_host') ?? 'example.imgix.net',
            $this->config->get('twill.imgix.use_https'),
            '',
            false
        );

        if ($this->config->get('twill.imgix.use_signed_urls')) {
            $urlBuilder->setSignKey($this->config->get('twill.imgix.sign_key'));
        }

        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @param string $id
     */
    public function getUrl($id, array $params = []): string
    {
        $defaultParams = $this->config->get('twill.imgix.default_params');
        $addParamsToSvgs = config('twill.imgix.add_params_to_svgs', false);

        if (! $addParamsToSvgs && Str::endsWith($id, '.svg')) {
            return $this->urlBuilder->createURL($id);
        }

        return $this->urlBuilder->createURL($id, array_replace($defaultParams, $params));
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
        $defaultParams = $this->config->get('twill.imgix.lqip_default_params');

        $cropParams = Arr::has($params, $this->cropParamsKeys) ? $this->getCrop($params) : [];

        $params = Arr::except($params, $this->cropParamsKeys);

        return $this->getUrl($id, array_replace($defaultParams, $params + $cropParams));
    }

    /**
     * @param string $id
     */
    public function getSocialUrl($id, array $params = []): string
    {
        $defaultParams = $this->config->get('twill.imgix.social_default_params');

        $cropParams = Arr::has($params, $this->cropParamsKeys) ? $this->getCrop($params) : [];

        $params = Arr::except($params, $this->cropParamsKeys);

        return $this->getUrl($id, array_replace($defaultParams, $params + $cropParams));
    }

    /**
     * @param string $id
     */
    public function getCmsUrl($id, array $params = []): string
    {
        $defaultParams = $this->config->get('twill.imgix.cms_default_params');

        $cropParams = Arr::has($params, $this->cropParamsKeys) ? $this->getCrop($params) : [];

        $params = Arr::except($params, $this->cropParamsKeys);

        return $this->getUrl($id, array_replace($defaultParams, $params + $cropParams));
    }

    /**
     * @param string $id
     */
    public function getRawUrl($id): string
    {
        return $this->urlBuilder->createURL($id);
    }

    /**
     * @param string $id
     * @return array|null
     */
    public function getDimensions($id)
    {
        $url = $this->urlBuilder->createURL($id, ['fm' => 'json']);

        try {
            $imageMetadata = json_decode(file_get_contents($url), true);

            return [
                'width' => $imageMetadata['PixelWidth'],
                'height' => $imageMetadata['PixelHeight'],
            ];
        } catch (\Exception) {
            try {
                list($width, $height) = getimagesize($url);

                return [
                    'width' => $width,
                    'height' => $height,
                ];
            } catch (\Exception) {
                return [
                    'width' => 0,
                    'height' => 0,
                ];
            }
        }
    }

    /**
     * @param mixed[] $crop_params
     * @return mixed[]
     */
    protected function getCrop(array $crop_params): array
    {
        if (! empty($crop_params)) {
            return ['rect' => $crop_params['crop_x'] . ',' .
                $crop_params['crop_y'] . ',' .
                $crop_params['crop_w'] . ',' .
                $crop_params['crop_h'],
            ];
        }

        return [];
    }

    /**
     * @param mixed[] $crop_params
     * @return array<string, string>
     */
    protected function getFocalPointCrop(array $crop_params, int $width, int $height): array
    {
        if (! empty($crop_params)) {
            // determine center coordinates of user crop and express it in term of original image width and height percentage
            $fpX = ($crop_params['crop_w'] / 2 + $crop_params['crop_x']) / $width;
            $fpY = ($crop_params['crop_h'] / 2 + $crop_params['crop_y']) / $height;

            // determine focal zoom
            if ($crop_params['crop_w'] > $crop_params['crop_h']) {
                $fpZ = $width / $crop_params['crop_w'];
            } else {
                $fpZ = $height / $crop_params['crop_h'];
            }

            $params = ['fp-x' => $fpX, 'fp-y' => $fpY, 'fp-z' => $fpZ];

            return array_map(function ($param) {
                return number_format($param, 4, '.', '');
            }, $params) + ['crop' => 'focalpoint', 'fit' => 'crop'];
        }

        return [];
    }
}
