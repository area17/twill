<?php

namespace A17\CmsToolkit\Services\MediaLibrary;

interface ImageServiceInterface
{
    public function getUrl($id, array $params = []);
    public function getUrlWithCrop($id, array $crop_params, array $params = []);
    public function getUrlWithFocalCrop($id, array $cropParams, $width, $height, array $params = []);
    public function getLQIPUrl($id, array $params = []);
    public function getSocialUrl($id, array $params = []);
    public function getCmsUrl($id, array $params = []);
    public function getRawUrl($id);
    public function getDimensions($id);
    public function getSocialFallbackUrl();
    public function getTransparentFallbackUrl();

}
