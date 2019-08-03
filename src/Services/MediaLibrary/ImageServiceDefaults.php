<?php

namespace A17\Twill\Services\MediaLibrary;

trait ImageServiceDefaults
{

    protected $cropParamsKeys = [
        'crop_x',
        'crop_y',
        'crop_w',
        'crop_h',
    ];

    /**
     * @return string
     */
    public function getSocialFallbackUrl()
    {
        if ($id = config("twill.seo.image_default_id")) {
            return $this->getSocialUrl($id);
        }

        return config("twill.seo.image_local_fallback");
    }

    /**
     * @return string
     */
    public function getTransparentFallbackUrl()
    {
        return "data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7";
    }
}
