<?php

namespace A17\Twill\Services\MediaLibrary;

use Illuminate\Support\Facades\Facade;

class ImageService extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'imageService';
    }
}
