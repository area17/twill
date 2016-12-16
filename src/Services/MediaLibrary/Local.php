<?php

namespace A17\CmsToolkit\Services\MediaLibrary;

class Local implements ImageServiceInterface
{
    use ImageServiceDefaults;

    public function getUrl($id, array $params = [])
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
        return '/' . config('cms-toolkit.media_library.local_path') . $id;
    }

    public function getDimensions($id)
    {
        return null;
    }
}
