<?php

namespace A17\Twill\Services\MediaLibrary;

use Illuminate\Support\Arr;

class TwicPics implements ImageServiceInterface
{
    use ImageServiceDefaults;

    public function __construct(protected TwicPicsParamsProcessor $paramsProcessor)
    {
    }

    /**
     * @param string $id
     */
    public function getUrl($id, array $params = []): string
    {
        $defaultParams = config('twill.twicpics.default_params');

        return $this->createUrl($id, array_replace($defaultParams, $params));
    }

    /**
     * @param string $id
     * @param array $crop_params
     */
    public function getUrlWithCrop($id, array $cropParams, array $params = []): string
    {
        return $this->getUrl($id, $this->getCrop($cropParams) + $params);
    }

    /**
     * @param string $id
     * @param int $width
     * @param int $height
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
        $defaultParams = config('twill.twicpics.lqip_default_params');

        return $this->getUrlWithDefaultParams($id, $params, $defaultParams);
    }

    /**
     * @param string $id
     */
    public function getSocialUrl($id, array $params = []): string
    {
        $defaultParams = config('twill.twicpics.social_default_params');

        return $this->getUrlWithDefaultParams($id, $params, $defaultParams);
    }

    /**
     * @param string $id
     */
    public function getCmsUrl($id, array $params = []): string
    {
        $defaultParams = config('twill.twicpics.cms_default_params');

        return $this->getUrlWithDefaultParams($id, $params, $defaultParams);
    }

    /**
     * @param string $id
     */
    public function getRawUrl($id): string
    {
        $domain = config('twill.twicpics.domain');
        $path = config('twill.twicpics.path');

        if (! empty($path)) {
            $path = sprintf('%s/', $path);
        }

        return sprintf('https://%s/%s%s', $domain, $path, $id);
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
     * @param mixed[] $params
     */
    protected function createUrl(string $id, array $params = []): string
    {
        $rawUrl = $this->getRawUrl($id);

        $apiVersion = config('twill.twicpics.api_version');

        $params = $this->paramsProcessor->process($params);

        $manipulations = collect($params)->map(function ($value, $key): string {
            return sprintf('%s=%s', $key, $value);
        })->join('/');

        return sprintf('%s?twic=%s/%s', $rawUrl, $apiVersion, $manipulations);
    }

    /**
     * @param mixed[] $params
     * @param mixed[] $defaultParams
     */
    protected function getUrlWithDefaultParams(string $id, array $params = [], array $defaultParams = []): string
    {
        $cropParams = Arr::has($params, $this->cropParamsKeys) ? $this->getCrop($params) : [];

        $params = Arr::except($params, $this->cropParamsKeys);

        return $this->getUrl($id, array_replace($defaultParams, $cropParams + $params));
    }

    /**
     * @param mixed[] $cropParams
     * @return mixed[]
     */
    protected function getCrop(array $cropParams): array
    {
        $cropW = $cropParams['crop_w'] ?? null;
        $cropH = $cropParams['crop_h'] ?? null;
        $cropX = $cropParams['crop_x'] ?? null;
        $cropY = $cropParams['crop_y'] ?? null;

        if (!filled($cropW) || !filled($cropH)) {
            return [];
        }

        $expression = sprintf('%sx%s', $cropW, $cropH);

        if (filled($cropX) && filled($cropY)) {
            $expression .= sprintf('@%sx%s', $cropX, $cropY);
        }

        return ['crop' => $expression];
    }

    /**
     * @param mixed[] $cropParams
     * @return mixed[]
     */
    protected function getFocalPointCrop(array $cropParams, int $width, int $height): array
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

        return ['focus' => sprintf('%sx%s', $focusX, $focusY)];
    }
}
