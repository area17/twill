<?php

namespace A17\Twill\Exceptions;

use Exception;

class MediaCropNotFoundException extends Exception
{
    public function __construct(string $crop)
    {
        $message = "Found media but could not find the crop '$crop'";
        parent::__construct($message);
    }
}
