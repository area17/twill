<?php

namespace A17\Twill\Repositories;

use A17\Twill\Models\Media;
use A17\Twill\Repositories\Behaviors\HandleTags;
use ImageService;
use Storage;

class MediaRepository extends ModuleRepository
{
    use HandleTags;

    public function __construct(Media $model)
    {
        $this->model = $model;
    }

    public function filter($query, array $scopes = [])
    {
        $this->searchIn($query, $scopes, 'search', ['alt_text', 'filename', 'caption']);
        return parent::filter($query, $scopes);
    }

    public function delete($id)
    {
        if (($object = $this->model->find($id)) != null) {
            if ($object->canDeleteSafely()) {
                $storageId = $object->uuid;
                if ($object->delete() && config('twill.media_library.cascade_delete')) {
                    Storage::disk(config('twill.media_library.disk'))->delete($storageId);
                }
                return true;
            }
        }

        return false;
    }

    public function prepareFieldsBeforeCreate($fields)
    {
        if (config('twill.media_library.init_alt_text_from_filename', true)) {
            $fields['alt_text'] = $this->model->altTextFrom($fields['filename']);
        }

        // if we were not able to determine dimensions with the browser File API, let's ask the Image service
        if (!isset($fields['width']) || !isset($fields['height'])) {
            $dimensions = ImageService::getDimensions($fields['uuid']);
            $fields['width'] = $dimensions['width'] ?? 0;
            $fields['height'] = $dimensions['height'] ?? 0;
        }

        return $fields;
    }

}
