<?php

namespace Sb4yd3e\Twill\Services\MediaLibrary;

trait ImageServiceDefaults
{
    public function getSocialFallbackUrl()
    {
        if ($id = config("twill.seo.image_default_id")) {
            return $this->getSocialUrl($id);
        }

        return config("twill.seo.image_local_fallback");
    }

    public function getTransparentFallbackUrl()
    {
        return "data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7";
    }
}
