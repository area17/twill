<?php

namespace A17\Twill\Repositories;

use A17\Twill\Models\Media;
use A17\Twill\Repositories\Behaviors\HandleTags;
use A17\Twill\Services\MediaLibrary\ImageService;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaRepository extends ModuleRepository
{
    use HandleTags;

    public function __construct(Media $model)
    {
        $this->model = $model;
    }

    public function afterDelete($object): void
    {
        $storageId = $object->uuid;
        if (Config::get('twill.media_library.cascade_delete')) {
            Storage::disk(Config::get('twill.media_library.disk'))->delete($storageId);
            // Get the folder and remove it as well if empty.
            $folder = Str::finish(Str::beforeLast($storageId, '/'), '/');
            if (empty(Storage::disk(Config::get('twill.media_library.disk'))->files($folder))) {
                Storage::disk(Config::get('twill.media_library.disk'))->deleteDirectory($folder);
            }
        }
    }

    public function prepareFieldsBeforeCreate(array $fields): array
    {
        if (Config::get('twill.media_library.init_alt_text_from_filename', true)) {
            $fields['alt_text'] = $this->model->altTextFrom($fields['filename']);
        }

        // if we were not able to determine dimensions with the browser File API, let's ask the Image service
        if (!isset($fields['width'], $fields['height'])) {
            $dimensions = ImageService::getDimensions($fields['uuid']);
            $fields['width'] = $dimensions['width'] ?? 0;
            $fields['height'] = $dimensions['height'] ?? 0;
        }

        return $fields;
    }
}
