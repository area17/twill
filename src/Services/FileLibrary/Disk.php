<?php

namespace A17\Twill\Services\FileLibrary;

use Illuminate\Contracts\Filesystem\Factory as FilesystemManager;

class Disk implements FileServiceInterface
{
    /**
     * @var FilesystemManager
     */
    protected $filesystemManager;

    /**
     * @param FilesystemManager $filesystemManager
     */
    public function __construct(FilesystemManager $filesystemManager)
    {
        $this->filesystemManager = $filesystemManager;
    }

    /**
     * @param mixed $id
     * @return mixed
     */
    public function getUrl($id)
    {
        return $this->filesystemManager->disk(config('twill.file_library.disk'))->url($id);
    }
}
