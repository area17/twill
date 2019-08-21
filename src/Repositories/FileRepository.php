<?php

namespace A17\Twill\Repositories;

use A17\Twill\Models\File;
use A17\Twill\Repositories\Behaviors\HandleTags;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

class FileRepository extends ModuleRepository
{
    use HandleTags;

    /**
     * @param File $model
     */
    public function __construct(File $model)
    {
        $this->model = $model;
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
        if (Config::get('twill.file_library.cascade_delete')) {
            Storage::disk(Config::get('twill.file_library.disk'))->delete($storageId);
        }
    }

    /**
     * @param array $fields
     * @return array
     */
    public function prepareFieldsBeforeCreate($fields)
    {
        if (!isset($fields['size'])) {
            $fields['size'] = Storage::disk(Config::get('twill.file_library.disk'))->size($fields['uuid']);
        }

        return $fields;
    }
}
