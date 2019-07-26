<?php

namespace A17\Twill\Repositories;

use A17\Twill\Models\Media;
use A17\Twill\Repositories\Behaviors\HandleTags;
use Illuminate\Config\Repository as Config;
use Illuminate\Contracts\Filesystem\Factory as FilesystemManager;
use ImageService;

class MediaRepository extends ModuleRepository
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
     * @param Media $model
     * @param FilesystemManager $filesystemManager
     */
    public function __construct(Media $model, FilesystemManager $filesystemManager, Config $config)
    {
        $this->model = $model;
        $this->filesystemManager = $filesystemManager;
        $this->config = $config;
    }

    public function filter($query, array $scopes = [])
    {
        $this->searchIn($query, $scopes, 'search', ['alt_text', 'filename', 'caption']);
        return parent::filter($query, $scopes);
    }

    public function afterDelete($object)
    {
        $storageId = $object->uuid;
        if ($this->config->get('twill.media_library.cascade_delete')) {
            $this->filesystemManager->disk($this->config->get('twill.media_library.disk'))->delete($storageId);
        }
    }

    public function prepareFieldsBeforeCreate($fields)
    {
        if ($this->config->get('twill.media_library.init_alt_text_from_filename', true)) {
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
