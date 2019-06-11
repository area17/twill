<?php

namespace A17\Twill\Services\FileLibrary;

use Storage;

class Disk implements FileServiceInterface
{
    /**
     * @param mixed $id
     * @return mixed
     */
    public function getUrl($id)
    {
        return Storage::disk(config('twill.file_library.disk'))->url($id);
    }
}
