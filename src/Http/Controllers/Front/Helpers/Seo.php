<?php

namespace A17\CmsToolkit\Http\Controllers\Front\Helpers;

class Seo
{
    public $title;
    public $description;
    public $nofollow = false;
    public $image;
    public $width;
    public $height;

    public function setTitle($title)
    {
        if (!empty($title)) {
            $this->title = $title;
        }
    }

    public function setDescription($description)
    {
        if (!empty($description)) {
            $this->description = $description;
        }
    }
}
