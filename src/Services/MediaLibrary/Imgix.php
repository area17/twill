<?php

namespace A17\CmsToolkit\Services\MediaLibrary;

use Imgix\ShardStrategy;
use Imgix\UrlBuilder;

class Imgix implements ImageServiceInterface
{
    use ImageServiceDefaults;

    private $urlBuilder;

    public function __construct()
    {
        $urlBuilder = new UrlBuilder(config('cms-toolkit.imgix.source_host'), config('cms-toolkit.imgix.use_https'), '', ShardStrategy::CRC, false);

        if (config('cms-toolkit.imgix.use_signed_urls')) {
            $urlBuilder->setSignKey(config('cms-toolkit.imgix.sign_key'));
        }

        $this->urlBuilder = $urlBuilder;
    }

    public function getUrl($id, array $params = [])
    {
        $defaultParams = config('cms-toolkit.imgix.default_params');
        return $this->urlBuilder->createURL($id, ends_with($id, '.svg') ? [] : array_replace($defaultParams, $params));
    }

    public function getLQIPUrl($id, array $params = [])
    {
        $defaultParams = config('cms-toolkit.imgix.lqip_default_params');
        return $this->getUrl($id, array_replace($defaultParams, $params));
    }

    public function getSocialUrl($id, array $params = [])
    {
        $defaultParams = config('cms-toolkit.imgix.social_default_params');
        return $this->getUrl($id, array_replace($defaultParams, $params));
    }

    public function getCmsUrl($id, array $params = [])
    {
        $defaultParams = config('cms-toolkit.imgix.cms_default_params');
        return $this->getUrl($id, array_replace($defaultParams, $params));
    }

    public function getRawUrl($id)
    {
        return $this->urlBuilder->createURL($id);
    }

    public function getDimensions($id)
    {
        $url = $this->urlBuilder->createURL($id, ['fm' => 'json']);

        $imageMetadata = json_decode(file_get_contents($url), true);

        return [
            'width' => $imageMetadata['PixelWidth'],
            'height' => $imageMetadata['PixelHeight'],
        ];
    }
}
