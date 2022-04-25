<?php

namespace A17\Twill\Services\FileLibrary;

use Illuminate\Config\Repository as Config;
use Illuminate\Contracts\Filesystem\Factory as FilesystemManager;

class Disk implements FileServiceInterface
{
    public function __construct(protected FilesystemManager $filesystemManager, protected Config $config)
    {
    }

    /**
     * @param mixed $id
     * @return mixed
     */
    public function getUrl($id)
    {
        return $this->filesystemManager->disk($this->config->get('twill.file_library.disk'))->url($id);
    }
}
