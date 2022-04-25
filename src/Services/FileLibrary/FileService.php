<?php

namespace A17\Twill\Services\FileLibrary;

use Illuminate\Support\Facades\Facade;

class FileService extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'fileService';
    }
}
