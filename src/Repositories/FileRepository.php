<?php

namespace A17\Twill\Repositories;

use A17\Twill\Models\File;
use A17\Twill\Repositories\Behaviors\HandleTags;
use Illuminate\Config\Repository as Config;
use Illuminate\Contracts\Filesystem\Factory as FilesystemManager;
use Illuminate\Database\DatabaseManager as DB;
use Illuminate\Foundation\Application;
use Psr\Log\LoggerInterface as Logger;

class FileRepository extends ModuleRepository
{
    use HandleTags;

    /**
     * @var FilesystemManager
     */
    protected $filesystemManager;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @param DB $db
     * @param Logger $logger
     * @param Application $app
     * @param Config $config
     * @param File $model
     * @param FilesystemManager $filesystemManager
     */
    public function __construct(File $model, FilesystemManager $filesystemManager, Config $config)
    {
        $this->model = $model;
        $this->filesystemManager = $filesystemManager;
        $this->config = $config;
    }

    /**
     * @param \Illuminate\Database\Query\Builder $query
     * @param array $scopes
     * @return \Illuminate\Database\Query\Builder
     */
    public function filter($query, array $scopes = [])
    {
        $this->searchIn($query, $scopes, 'search', ['filename']);
        return parent::filter($query, $scopes);
    }

    /**
     * @param A17\Twill\Models\File $object
     * @return void
     */
    public function afterDelete($object)
    {
        $storageId = $object->uuid;
        if ($this->config->get('twill.file_library.cascade_delete')) {
            $this->filesystemManager->disk($this->config->get('twill.file_library.disk'))->delete($storageId);
        }
    }

    /**
     * @param array $fields
     * @return array
     */
    public function prepareFieldsBeforeCreate($fields)
    {
        if (!isset($fields['size'])) {
            $fields['size'] = $this->filesystemManager->disk($this->config->get('twill.file_library.disk'))->size($fields['uuid']);
        }

        return $fields;
    }
}
