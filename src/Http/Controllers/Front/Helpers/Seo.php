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

    public function setTitle(string $title): void
    {
        if (!empty($title)) {
            $this->title = $title;
        }
    }

    public function setDescription(string $description): void
    {
        if (!empty($description)) {
            $this->description = $description;
        }
    }
}
