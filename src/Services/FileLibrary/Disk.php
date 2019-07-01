<?php

namespace A17\Twill\Services\FileLibrary;

use Illuminate\Config\Repository as Config;
use Illuminate\Contracts\Filesystem\Factory as FilesystemManager;

class Disk implements FileServiceInterface
{
    /**
     * @var FilesystemManager
     */
    protected $filesystemManager;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @param FilesystemManager $filesystemManager
     * @param Config $config
     */
    public function __construct(FilesystemManager $filesystemManager, Config $config)
    {
        $this->filesystemManager = $filesystemManager;
        $this->config = $config;
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
