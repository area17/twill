<?php

namespace A17\Twill\Services\FileLibrary;

use Illuminate\Support\Facades\Storage;

class Disk implements FileServiceInterface
{
    public function getUrl($id)
    {
        return Storage::disk(config('twill.file_library.disk'))->url($id);
    }
}
