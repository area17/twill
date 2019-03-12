<?php

namespace Sb4yd3e\Twill\Services\MediaLibrary;

use Illuminate\Support\Facades\Facade;

class ImageService extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'imageService';
    }
}
