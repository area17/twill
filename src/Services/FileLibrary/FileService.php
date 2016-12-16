<?php

namespace A17\CmsToolkit\Services\FileLibrary;

use Illuminate\Support\Facades\Facade;

class FileService extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'fileService';
    }
}
