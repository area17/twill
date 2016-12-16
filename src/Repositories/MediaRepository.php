<?php

namespace A17\CmsToolkit\Repositories;

use A17\CmsToolkit\Models\Media;
use A17\CmsToolkit\Repositories\Behaviors\HandleTags;
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
        if (isset($scopes['search'])) {
            $query->orWhereHas('tags', function ($query) use ($scopes) {
                $query->where('slug', 'like', '%' . $scopes['search'] . '%');
            });
        }

        $this->searchIn($query, $scopes, 'search', ['alt_text', 'filename', 'caption']);

        return parent::filter($query, $scopes);
    }

    public function delete($id)
    {
        if (($object = $this->model->find($id)) != null) {
            if ($object->canDeleteSafely()) {
                $storageId = $object->uuid;
                if ($object->delete() && config('cms-toolkit.media_library.cascade_delete')) {
                    Storage::disk(config('cms-toolkit.media_library.disk'))->delete($storageId);
                }
            }
        }
    }

    public function prepareFieldsBeforeCreate($fields)
    {
        $fields['alt_text'] = $this->model->altTextFrom($fields['filename']);

        // if we were not able to determine dimensions with the browser File API, let's ask the Image service
        if (!isset($fields['width']) || !isset($fields['height'])) {
            $dimensions = ImageService::getDimensions($fields['uuid']);
            $fields['width'] = $dimensions['width'] ?? 0;
            $fields['height'] = $dimensions['height'] ?? 0;
        }

        return $fields;
    }

}
