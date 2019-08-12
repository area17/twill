<?php

namespace A17\Twill\Services\MediaLibrary;

use Illuminate\Config\Repository as Config;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use League\Glide\Responses\LaravelResponseFactory;
use League\Glide\ServerFactory;
use League\Glide\Signatures\SignatureFactory;
use League\Glide\Urls\UrlBuilderFactory;

class Glide implements ImageServiceInterface
{
    use ImageServiceDefaults;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var League\Glide\Server
     */
    private $server;

    /**
     * @var UrlBuilder
     */
    private $urlBuilder;

    /**
     * @param Config $config
     * @param Application $app
     * @param Request $request
     */
    public function __construct(Config $config, Application $app, Request $request)
    {
        $this->config = $config;
        $this->app = $app;
        $this->request = $request;

        $baseUrl = join([
            rtrim($this->config->get('twill.glide.base_url'), '/'),
            ltrim($this->config->get('twill.glide.base_path'), '/'),
        ], '/');

        $this->server = ServerFactory::create([
            'response' => new LaravelResponseFactory($this->request),
            'source' => $this->config->get('twill.glide.source'),
            'cache' => $this->config->get('twill.glide.cache'),
            'cache_path_prefix' => $this->config->get('twill.glide.cache_path_prefix'),
            'base_url' => $baseUrl,
        ]);

        $this->urlBuilder = UrlBuilderFactory::create(
            $baseUrl,
            $this->config->get('twill.glide.use_signed_urls') ? $this->config->get('twill.glide.sign_key') : null
        );
    }

    /**
     * @param string $path
     * @return StreamedResponse
     */
    public function render($path)
    {
        if ($this->config->get('twill.glide.use_signed_urls')) {
            SignatureFactory::create($this->config->get('twill.glide.sign_key'))->validateRequest($this->config->get('twill.glide.base_path') . '/' . $path, $this->request->all());
        }

        return $this->server->getImageResponse($path, $this->request->all());
    }

    /**
     * @param string $id
     * @param array $params
     * @return string
     */
    public function getUrl($id, array $params = [])
    {
        $defaultParams = config('twill.glide.default_params');
        return $this->urlBuilder->getUrl($id, Str::endsWith($id, '.svg') ? [] : array_replace($defaultParams, $params));
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
        $defaultParams = config('twill.glide.lqip_default_params');

        $cropParams = Arr::has($params, $this->cropParamsKeys) ? $this->getCrop($params) : [];

        $params = Arr::except($params, $this->cropParamsKeys);

        return $this->getUrl($id, array_replace($defaultParams, $params + $cropParams));
    }

    /**
     * @param string $id
     * @param array $params
     * @return string
     */
    public function getSocialUrl($id, array $params = [])
    {
        $defaultParams = config('twill.glide.social_default_params');

        $cropParams = Arr::has($params, $this->cropParamsKeys) ? $this->getCrop($params) : [];

        $params = Arr::except($params, $this->cropParamsKeys);

        return $this->getUrl($id, array_replace($defaultParams, $params + $cropParams));
    }

    /**
     * @param string $id
     * @return string
     */
    public function getCmsUrl($id, array $params = [])
    {
        $defaultParams = config('twill.glide.cms_default_params');

        $cropParams = Arr::has($params, $this->cropParamsKeys) ? $this->getCrop($params) : [];

        $params = Arr::except($params, $this->cropParamsKeys);

        return $this->getUrl($id, array_replace($defaultParams, $params + $cropParams));
    }

    /**
     * @param string $id
     * @return array|null
     */
    public function getRawUrl($id)
    {
        return $this->urlBuilder->getUrL($id);
    }

    /**
     * @param string $id
     * @return array
     */
    public function getDimensions($id)
    {
        $url = $this->urlBuilder->getUrL($id);

        try {
            list($w, $h) = getimagesize($url);

            return [
                'width' => $w,
                'height' => $h,
            ];
        } catch (\Exception $e) {
            return [
                'width' => 0,
                'height' => 0,
            ];
        }
    }

    /**
     * @param array $crop_params
     * @return array
     */
    protected function getCrop($crop_params)
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
     * @param array $crop_params
     * @param int $width
     * @param int $height
     * @return array
     */
    protected function getFocalPointCrop($crop_params, $width, $height)
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

            $params = ['fit' => 'crop-' . $fpX . '-' . $fpY . '-' . $fpZ];

            return $params;
        }

        return [];
    }
}
