<?php

namespace A17\CmsToolkit\Services\MediaLibrary;

use Illuminate\Support\Facades\Facade;

class ImageService extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'imageService';
    }
}
