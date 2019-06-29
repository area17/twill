<?php

namespace A17\Twill\Services\MediaLibrary;

use Illuminate\Support\Str;
use Imgix\ShardStrategy;
use Imgix\UrlBuilder;

class Imgix implements ImageServiceInterface
{
    use ImageServiceDefaults;

    /**
     * @var UrlBuilder
     */
    private $urlBuilder;

    public function __construct()
    {
        $urlBuilder = new UrlBuilder(config('twill.imgix.source_host'), config('twill.imgix.use_https'), '', ShardStrategy::CRC, false);

        if (config('twill.imgix.use_signed_urls')) {
            $urlBuilder->setSignKey(config('twill.imgix.sign_key'));
        }

        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @param string $id
     * @param array $params
     * @return string
     */
    public function getUrl($id, array $params = [])
    {
        $defaultParams = config('twill.imgix.default_params');
        return $this->urlBuilder->createURL($id, Str::endsWith($id, '.svg') ? [] : array_replace($defaultParams, $params));
    }

    /**
     * @param string $id
     * @param array $cropParams
     * @param array $params
     * @return string
     */
    public function getUrlWithCrop($id, array $cropParams, array $params = [])
    {
        return $this->getUrl($id, $this->getCrop($cropParams) + $params);
    }

    /**
     * @param string $id
     * @param array $cropParams
     * @param mixed $width
     * @param mixed $height
     * @param array $params
     * @return string
     */
    public function getUrlWithFocalCrop($id, array $cropParams, $width, $height, array $params = [])
    {
        return $this->getUrl($id, $this->getFocalPointCrop($cropParams, $width, $height) + $params);
    }

    /**
     * @param string $id
     * @param array $params
     * @return string
     */
    public function getLQIPUrl($id, array $params = [])
    {
        $defaultParams = config('twill.imgix.lqip_default_params');
        return $this->getUrl($id, array_replace($defaultParams, $params));
    }

    /**
     * @param string $id
     * @param array $params
     * @return string
     */
    public function getSocialUrl($id, array $params = [])
    {
        $defaultParams = config('twill.imgix.social_default_params');
        return $this->getUrl($id, array_replace($defaultParams, $params));
    }

    /**
     * @param string $id
     * @param array $params
     * @return string
     */
    public function getCmsUrl($id, array $params = [])
    {
        $defaultParams = config('twill.imgix.cms_default_params');
        return $this->getUrl($id, array_replace($defaultParams, $params));
    }

    /**
     * @param string $id
     * @return string
     */
    public function getRawUrl($id)
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
        } catch (\Exception $e) {
            try {
                list($width, $height) = getimagesize($url);
                return [
                    'width' => $width,
                    'height' => $height,
                ];
            } catch (\Exception $e) {
                return [
                    'width' => 0,
                    'height' => 0,
                ];
            }
        }
    }

    /**
     * @param array $crop_params
     * @return array
     */
    protected function getCrop($crop_params)
    {
        if (!empty($crop_params)) {
            return ['rect' => $crop_params['crop_x'] . ',' . $crop_params['crop_y'] . ',' . $crop_params['crop_w'] . ',' . $crop_params['crop_h']];
        }

        return [];
    }

    /**
     * @param array $crop_params
     * @param int $width
     * @param int $height
     * @return array
     */
    protected function getFocalPointCrop($crop_params, $width, $height)
    {
        if (!empty($crop_params)) {
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
                return number_format($param, 4, ".", "");
            }, $params) + ['crop' => 'focalpoint', 'fit' => 'crop'];
        }

        return [];
    }
}
