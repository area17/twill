<?php

namespace A17\Twill\Exceptions;

class MediaCropNotFoundException extends \Exception
{
    public function __construct(string $crop)
    {
        $message = sprintf('Found media but could not find the crop \'%s\'', $crop);
        parent::__construct($message);
    }
}
