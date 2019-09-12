<?php

namespace A17\Twill\Http\Controllers\Front\Helpers;

class Seo
{
    /**
     * @var string|null
     */
    public $title;

    /**
     * @var string|null
     */
    public $description;

    /**
     * @var bool
     */
    public $nofollow = false;

    /**
     * @var \A17\Twill\Models\Media|null
     */
    public $image;

    /**
     * @var int
     */
    public $width;

    /**
     * @var int
     */
    public $height;

    /**
     * @param string $title
     * @return void
     */
    public function setTitle($title)
    {
        if (!empty($title)) {
            $this->title = $title;
        }
    }

    /**
     * @param string $description
     * @return void
     */
    public function setDescription($description)
    {
        if (!empty($description)) {
            $this->description = $description;
        }
    }
}
