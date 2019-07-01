<?php

namespace A17\Twill\Repositories;

use A17\Twill\Models\Media;
use A17\Twill\Repositories\Behaviors\HandleTags;
use Illuminate\Config\Repository as Config;
use Illuminate\Contracts\Filesystem\Factory as FilesystemManager;
use Illuminate\Database\DatabaseManager as DB;
use Illuminate\Foundation\Application;
use ImageService;
use Psr\Log\LoggerInterface as Logger;


class MediaRepository extends ModuleRepository
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
     * @param Media $model
     * @param FilesystemManager $filesystemManager
     */
    public function __construct(
        DB $db,
        Logger $logger,
        Application $app,
        Config $config,
        Media $model,
        FilesystemManager $filesystemManager
    ) {
        parent::__construct($db, $logger, $app, $config);

        $this->model = $model;
        $this->filesystemManager = $filesystemManager;
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
                if ($object->delete() && $this->config->get('twill.media_library.cascade_delete')) {
                    $this->filesystemManager->disk($this->config->get('twill.media_library.disk'))->delete($storageId);
                }
                return true;
            }
        }

        return false;
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
