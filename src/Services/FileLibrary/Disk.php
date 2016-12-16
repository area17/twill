<?php

namespace A17\CmsToolkit\Services\FileLibrary;

use Storage;

class Disk implements FileServiceInterface
{
    public function getUrl($id)
    {
        return Storage::disk(config('cms-toolkit.file_library.disk'))->url($id);
    }
}
