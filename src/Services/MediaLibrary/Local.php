<?php

namespace A17\Twill\Services\MediaLibrary;

use Illuminate\Support\Facades\Storage;


class Local implements ImageServiceInterface
{
    use ImageServiceDefaults;

    public function getUrl($id, array $params = [])
    {
        return $this->getRawUrl($id);
    }

    public function getUrlWithCrop($id, array $crop_params, array $params = [])
    {
        return $this->getRawUrl($id);
    }

    public function getUrlWithFocalCrop($id, array $cropParams, $width, $height, array $params = [])
    {
        return $this->getRawUrl($id);
    }

    public function getLQIPUrl($id, array $params = [])
    {
        return $this->getRawUrl($id);
    }

    public function getSocialUrl($id, array $params = [])
    {
        return $this->getRawUrl($id);
    }

    public function getCmsUrl($id, array $params = [])
    {
        return $this->getRawUrl($id);
    }

    public function getRawUrl($id)
    {
        return Storage::disk(config('twill.media_library.disk'))->url($id);
    }

    public function getDimensions($id)
    {
        return null;
    }
}
