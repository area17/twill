<?php

namespace A17\Twill\Repositories;

use A17\Twill\Models\File;
use A17\Twill\Repositories\Behaviors\HandleTags;
use Storage;

class FileRepository extends ModuleRepository
{
    use HandleTags;

    public function __construct(File $model)
    {
        $this->model = $model;
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
                    Storage::disk(config('twill.file_library.disk'))->delete($storageId);
                }
            }
        }
    }

    public function prepareFieldsBeforeCreate($fields)
    {
        if (!isset($fields['size'])) {
            $fields['size'] = Storage::disk(config('twill.file_library.disk'))->size($fields['uuid']);
        }

        return $fields;
    }
}
