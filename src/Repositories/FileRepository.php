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
     * @param DB $db
     * @param Logger $logger
     * @param Application $app
     * @param Config $config
     * @param File $model
     * @param FilesystemManager $filesystemManager
     */
    public function __construct(
        DB $db,
        Logger $logger,
        Application $app,
        Config $config,
        File $model,
        FilesystemManager $filesystemManager
    ) {
        parent::__construct($db, $logger, $app, $config);

        $this->model = $model;
        $this->filesystemManager = $filesystemManager;
    }

    public function filter($query, array $scopes = [])
    {
        $this->searchIn($query, $scopes, 'search', ['filename']);
        return parent::filter($query, $scopes);
    }

    public function delete($id)
    {
        if (($object = $this->model->find($id)) != null) {
            if ($object->canDeleteSafely()) {
                $storageId = $object->uuid;
                if ($object->delete() && $this->config->get('twill.file_library.cascade_delete')) {
                    $this->filesystemManager->disk($this->config->get('twill.file_library.disk'))->delete($storageId);
                }
            }
        }
    }

    public function prepareFieldsBeforeCreate($fields)
    {
        if (!isset($fields['size'])) {
            $fields['size'] = $this->filesystemManager->disk($this->config->get('twill.file_library.disk'))->size($fields['uuid']);
        }

        return $fields;
    }
}
