<?php

namespace A17\CmsToolkit\Services\MediaLibrary;

trait ImageServiceDefaults
{
    public function getSocialFallbackUrl()
    {
        if ($id = config("cms-toolkit.seo.image_default_id")) {
            return $this->getSocialUrl($id);
        }

        return config("cms-toolkit.seo.image_local_fallback");
    }

    public function getTransparentFallbackUrl()
    {
        return "data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7";
    }
}
