<?php

namespace Sb4yd3e\Twill\Services\FileLibrary;

use Storage;

class Disk implements FileServiceInterface
{
    public function getUrl($id)
    {
        return Storage::disk(config('twill.file_library.disk'))->url($id);
    }
}
