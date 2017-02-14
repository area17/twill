<?php

namespace A17\CmsToolkit\Services\BlockEditor\Blocks;

use A17\CmsToolkit\Repositories\MediaRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BaseBlock
{
    protected $type;
    protected $data;
    protected $locale;
    protected $options;

    protected $types = [];

    public function __construct($block, $options = [])
    {
        $this->type = $block['type'];
        $this->data = $block['data'];
        $this->locale = $block['locale'] ?? app()->getLocale();
        $this->options = $options;
    }

    public function renderToHtml()
    {
        if (in_array($this->type, $this->types)) {

            $method = $this->type . 'ToHtml';

            if (method_exists($this, $method)) {
                try {

                    $viewAsString = $this->$method()->render();

                } catch (\Exception $e) {

                    if (config('cms-toolkit.block-editor.show_render_errors')) {
                        return $e->getMessage();
                    }

                    return $this->$method();
                }

                return $viewAsString;
            }
        }

        return;
    }

    public function renderMetadatas()
    {
        if (in_array($this->type, $this->types)) {
            $method = $this->type . 'Metadatas';
            if (method_exists($this, $method)) {
                return $this->$method();
            }
        }

        return [];
    }

    protected function getInput($name)
    {
        return $this->data[$name . '_' . $this->locale] ?? '';
    }

    protected function getImage($id)
    {
        if (empty($id)) {
            return null;
        }

        try {
            return app(MediaRepository::class)->getById($id);
        } catch (ModelNotFoundException $e) {
            return null;
        }
    }

    // this is very dependant on using Imgix, let's try to abstract it using the ImageService sooner or later
    protected function getCrop($crop_data)
    {
        $crop = json_decode($crop_data, true);

        if (!empty($crop)) {
            $crop_params = array_map(function ($crop_coord) {
                return max(0, intval($crop_coord));
            }, $crop);

            return ['rect' => $crop_params['crop_x'] . ',' . $crop_params['crop_y'] . ',' . $crop_params['crop_w'] . ',' . $crop_params['crop_h']];
        }

        return [];
    }

    protected function getResourceId($name = 'resource_id')
    {
        $locale = $this->data['resource_locale'] ?? $this->locale;

        $locale = ($locale === "1" ? $this->locale : $locale);

        return $this->data[$name . '_' . $locale];
    }

}
