<?php

namespace A17\Twill\Services\MediaLibrary;

interface ImageServiceInterface
{
    /**
     * @param string $id
     * @return string
     */
    public function getUrl($id, array $params = []);

    /**
     * @param string $id
     * @return string
     */
    public function getUrlWithCrop($id, array $crop_params, array $params = []);

    /**
     * @param string $id
     * @param int $width
     * @param int $height
     * @return string
     */
    public function getUrlWithFocalCrop($id, array $cropParams, $width, $height, array $params = []);

    /**
     * @param string $id
     * @return string
     */
    public function getLQIPUrl($id, array $params = []);

    /**
     * @param string $id
     * @return string
     */
    public function getSocialUrl($id, array $params = []);

    /**
     * @param string $id
     * @return string
     */
    public function getCmsUrl($id, array $params = []);

    /**
     * @param string $id
     * @return string
     */
    public function getRawUrl($id);

    /**
     * @param string $id
     * @return array|null
     */
    public function getDimensions($id);

    /**
     * @return string
     */
    public function getSocialFallbackUrl();

    /**
     * @return string
     */
    public function getTransparentFallbackUrl();
}
