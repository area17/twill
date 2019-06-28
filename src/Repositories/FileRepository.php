<?php

namespace A17\Twill\Repositories;

use A17\Twill\Models\File;
use A17\Twill\Repositories\Behaviors\HandleTags;
use Illuminate\Contracts\Filesystem\Factory as FilesystemManager;

class FileRepository extends ModuleRepository
{
    use HandleTags;

    /**
     * @var FilesystemManager
     */
    protected $filesystemManager;

    /**
     * @param File $model
     * @param FilesystemManager $filesystemManager
     */
    public function __construct(File $model, FilesystemManager $filesystemManager)
    {
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
                if ($object->delete() && config('twill.file_library.cascade_delete')) {
                    $this->filesystemManager->disk(config('twill.file_library.disk'))->delete($storageId);
                }
            }
        }
    }

    public function prepareFieldsBeforeCreate($fields)
    {
        if (!isset($fields['size'])) {
            $fields['size'] = $this->filesystemManager->disk(config('twill.file_library.disk'))->size($fields['uuid']);
        }

        return $fields;
    }
}
