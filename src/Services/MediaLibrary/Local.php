<?php

namespace A17\Twill\Services\MediaLibrary;

use Illuminate\Support\Facades\Storage;

class Local implements ImageServiceInterface
{
    use ImageServiceDefaults;

    /**
     * @param string $id
     */
    public function getUrl($id, array $params = []): string
    {
        return $this->getRawUrl($id);
    }

    /**
     * @param string $id
     */
    public function getUrlWithCrop($id, array $crop_params, array $params = []): string
    {
        return $this->getRawUrl($id);
    }

    /**
     * @param string $id
     * @param int $width
     * @param int $height
     */
    public function getUrlWithFocalCrop($id, array $cropParams, $width, $height, array $params = []): string
    {
        return $this->getRawUrl($id);
    }

    /**
     * @param string $id
     */
    public function getLQIPUrl($id, array $params = []): string
    {
        return $this->getRawUrl($id);
    }

    /**
     * @param string $id
     */
    public function getSocialUrl($id, array $params = []): string
    {
        return $this->getRawUrl($id);
    }

    /**
     * @param string $id
     */
    public function getCmsUrl($id, array $params = []): string
    {
        return $this->getRawUrl($id);
    }

    /**
     * @param string $id
     */
    public function getRawUrl($id): string
    {
        return Storage::disk(config('twill.media_library.disk'))->url($id);
    }

    /**
     * @param string $id
     * @return array|null
     */
    public function getDimensions($id)
    {
        return null;
    }
}
