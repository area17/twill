<?php

namespace A17\Twill\Services\FileLibrary;

use Illuminate\Support\Facades\Facade;

/** @mixin FileServiceInterface */
class FileService extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'fileService';
    }
}
