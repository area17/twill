<?php

namespace A17\Twill\Services\MediaLibrary;

interface ImageServiceInterface
{
    /**
     * @return string
     */
    public function getUrl(string $id, array $params = []);

    /**
     * @return string
     */
    public function getUrlWithCrop(string $id, array $crop_params, array $params = []);

    /**
     * @return string
     */
    public function getUrlWithFocalCrop(string $id, array $cropParams, int $width, int $height, array $params = []);

    /**
     * @return string
     */
    public function getLQIPUrl(string $id, array $params = []);

    /**
     * @return string
     */
    public function getSocialUrl(string $id, array $params = []);

    /**
     * @return string
     */
    public function getCmsUrl(string $id, array $params = []);

    /**
     * @return string
     */
    public function getRawUrl(string $id);

    /**
     * @return array|null
     */
    public function getDimensions(string $id);

    /**
     * @return string
     */
    public function getSocialFallbackUrl();

    /**
     * @return string
     */
    public function getTransparentFallbackUrl();
}
