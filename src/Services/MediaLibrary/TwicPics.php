<?php

namespace A17\Twill\Services\MediaLibrary;

use Illuminate\Support\Arr;

class TwicPics implements ImageServiceInterface
{
    use ImageServiceDefaults;

    protected $paramsProcessor;

    public function __construct(TwicPicsParamsProcessor $paramsProcessor)
    {
        $this->paramsProcessor = $paramsProcessor;
    }

    /**
     * @param string $id
     * @return string
     */
    public function getUrl($id, array $params = [])
    {
        $defaultParams = config('twill.twicpics.default_params');

        return $this->createUrl($id, array_replace($defaultParams, $params));
    }

    /**
     * @param string $id
     * @param array $crop_params
     * @return string
     */
    public function getUrlWithCrop($id, array $cropParams, array $params = [])
    {
        return $this->getUrl($id, $this->getCrop($cropParams) + $params);
    }

    /**
     * @param string $id
     * @param int $width
     * @param int $height
     * @return string
     */
    public function getUrlWithFocalCrop($id, array $cropParams, $width, $height, array $params = [])
    {
        return $this->getUrl($id, $this->getFocalPointCrop($cropParams, $width, $height) + $params);
    }

    /**
     * @param string $id
     * @return string
     */
    public function getLQIPUrl($id, array $params = [])
    {
        $defaultParams = config('twill.twicpics.lqip_default_params');

        return $this->getUrlWithDefaultParams($id, $params, $defaultParams);
    }

    /**
     * @param string $id
     * @return string
     */
    public function getSocialUrl($id, array $params = [])
    {
        $defaultParams = config('twill.twicpics.social_default_params');

        return $this->getUrlWithDefaultParams($id, $params, $defaultParams);
    }

    /**
     * @param string $id
     * @return string
     */
    public function getCmsUrl($id, array $params = [])
    {
        $defaultParams = config('twill.twicpics.cms_default_params');

        return $this->getUrlWithDefaultParams($id, $params, $defaultParams);
    }

    /**
     * @param string $id
     * @return string
     */
    public function getRawUrl($id)
    {
        $domain = config('twill.twicpics.domain');
        $path = config('twill.twicpics.path');

        if (! empty($path)) {
            $path = "{$path}/";
        }

        return "https://{$domain}/{$path}{$id}";
    }

    /**
     * @param string $id
     * @return array|null
     */
    public function getDimensions($id)
    {
        return null;
    }

    /**
     * @param string $id
     * @param array $params
     * @return string
     */
    protected function createUrl($id, $params = [])
    {
        $rawUrl = $this->getRawUrl($id);

        $apiVersion = config('twill.twicpics.api_version');

        $params = $this->paramsProcessor->process($params);

        $manipulations = collect($params)->map(function ($value, $key) {
            return "{$key}={$value}";
        })->join('/');

        return "{$rawUrl}?twic={$apiVersion}/{$manipulations}";
    }

    /**
     * @param string $id
     * @param array $params
     * @param array $defaultParams
     * @return string
     */
    protected function getUrlWithDefaultParams($id, $params = [], $defaultParams = [])
    {
        $cropParams = Arr::has($params, $this->cropParamsKeys) ? $this->getCrop($params) : [];

        $params = Arr::except($params, $this->cropParamsKeys);

        return $this->getUrl($id, array_replace($defaultParams, $cropParams + $params));
    }

    /**
     * @param array $cropParams
     * @return array
     */
    protected function getCrop($cropParams)
    {
        $cropW = $cropParams['crop_w'] ?? null;
        $cropH = $cropParams['crop_h'] ?? null;
        $cropX = $cropParams['crop_x'] ?? null;
        $cropY = $cropParams['crop_y'] ?? null;

        if (!filled($cropW) || !filled($cropH)) {
            return [];
        }

        $expression = "{$cropW}x{$cropH}";

        if (filled($cropX) && filled($cropY)) {
            $expression .= "@{$cropX}x{$cropY}";
        }

        return ['crop' => $expression];
    }

    /**
     * @param array $cropParams
     * @param int $width
     * @param int $height
     * @return array
     */
    protected function getFocalPointCrop($cropParams, $width, $height)
    {
        if (empty($cropParams)) {
            return [];
        }

        $cropW = $cropParams['crop_w'] ?? 0;
        $cropH = $cropParams['crop_h'] ?? 0;
        $cropX = $cropParams['crop_x'] ?? 0;
        $cropY = $cropParams['crop_y'] ?? 0;

        $focusX = $cropW / 2 + $cropX;
        $focusY = $cropH / 2 + $cropY;

        return ['focus' => "{$focusX}x{$focusY}"];
    }
}
