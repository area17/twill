<?php

namespace Sb4yd3e\Twill\Services\FileLibrary;

use Illuminate\Support\Facades\Facade;

class FileService extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'fileService';
    }
}
