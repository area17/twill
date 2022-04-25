<?php

namespace A17\Twill\Repositories;

use A17\Twill\Models\File;
use A17\Twill\Repositories\Behaviors\HandleTags;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

class FileRepository extends ModuleRepository
{
    use HandleTags;

    public function __construct(File $model)
    {
        $this->model = $model;
    }

    public function filter(\Illuminate\Database\Eloquent\Builder $query, array $scopes = []): \Illuminate\Database\Eloquent\Builder
    {
        $this->searchIn($query, $scopes, 'search', ['filename']);
        return parent::filter($query, $scopes);
    }

    public function afterDelete(\A17\Twill\Models\File $object): void
    {
        $storageId = $object->uuid;
        if (Config::get('twill.file_library.cascade_delete')) {
            Storage::disk(Config::get('twill.file_library.disk'))->delete($storageId);
        }
    }

    /**
     * @param mixed[] $fields
     * @return mixed[]
     */
    public function prepareFieldsBeforeCreate(array $fields): array
    {
        if (!isset($fields['size'])) {
            $uuid = str_replace(Config::get('filesystems.disks.twill_file_library.root'), '', $fields['uuid']);
            $fields['size'] = Storage::disk(Config::get('twill.file_library.disk'))->size($uuid);
        }

        return $fields;
    }
}
