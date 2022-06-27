<?php

namespace A17\Twill\Repositories;

use A17\Twill\Models\File;
use A17\Twill\Repositories\Behaviors\HandleTags;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileRepository extends ModuleRepository
{
    use HandleTags;

    public function __construct(File $model)
    {
        $this->model = $model;
    }

    public function afterDelete($object): void
    {
        $storageId = $object->uuid;
        if (Config::get('twill.file_library.cascade_delete')) {
            Storage::disk(Config::get('twill.file_library.disk'))->delete($storageId);
            // Get the folder and remove it as well if empty.
            $folder = Str::finish(Str::beforeLast($storageId, '/'), '/');
            if (empty(Storage::disk(Config::get('twill.file_library.disk'))->files($folder))) {
                Storage::disk(Config::get('twill.file_library.disk'))->deleteDirectory($folder);
            }
        }
    }

    public function prepareFieldsBeforeCreate(array $fields): array
    {
        if (!isset($fields['size'])) {
            $uuid = str_replace(Config::get('filesystems.disks.twill_file_library.root'), '', $fields['uuid']);
            $fields['size'] = Storage::disk(Config::get('twill.file_library.disk'))->size($uuid);
        }

        return $fields;
    }
}
