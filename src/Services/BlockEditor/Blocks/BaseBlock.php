<?php

namespace A17\CmsToolkit\Services\BlockEditor\Blocks;

use A17\CmsToolkit\Repositories\MediaRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Log;

class BaseBlock
{
    protected $type;
    protected $data;
    protected $locale;
    protected $options;
    protected $customViewsNamespace;

    protected $types = [];

    public function __construct($block, $options = [])
    {
        $this->type = $block['type'];
        $this->data = $block['data'];
        $this->locale = $block['locale'] ?? app()->getLocale();
        $this->options = $options;
        $this->customViewsNamespace = config('cms-toolkit.block_editor.custom_views_namespace', 'blocks');
    }

    public function renderToHtml()
    {
        if (in_array($this->type, $this->types)) {

            $method = $this->type . 'ToHtml';

            if (method_exists($this, $method)) {
                try {

                    $viewAsString = $this->$method()->render();

                } catch (\Exception $e) {

                    if (config('cms-toolkit.block_editor.show_render_errors')) {
                        Log::debug($e);
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

    protected function getInput($name, $value = '')
    {
        return $this->data[$name . '_' . $this->locale] ?? $value;
    }

    protected function getValue($name, $value = null)
    {
        return $this->data[$name] ?? $value;
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

    // This function will be deprecated in a future release
    protected function getCrop($crop_data)
    {
        $crop_params = $this->getCropParams($crop_data);

        if (!empty($crop_params)) {
            return ['rect' => $crop_params['crop_x'] . ',' . $crop_params['crop_y'] . ',' . $crop_params['crop_w'] . ',' . $crop_params['crop_h']];
        }

        return [];
    }

    // This function will be deprecated in a future release
    protected function getFocalPointCropParams($crop_data, $media)
    {
        $crop = json_decode($crop_data, true);
        if (!empty($crop)) {
            $crop_params = array_map(function ($crop_coord) {
                return max(0, intval($crop_coord));
            }, $crop);

            // determine center coordinates of user crop and express it in term of original image width and height percentage
            $fpX = ($crop_params['crop_w'] / 2 + $crop_params['crop_x']) / $media->width;
            $fpY = ($crop_params['crop_h'] / 2 + $crop_params['crop_y']) / $media->height;

            // determine focal zoom
            if ($crop_params['crop_w'] > $crop_params['crop_h']) {
                $fpZ = $media->width / $crop_params['crop_w'];
            } else {
                $fpZ = $media->height / $crop_params['crop_h'];
            }

            $params = ['fp-x' => $fpX, 'fp-y' => $fpY, 'fp-z' => $fpZ];

            return array_map(function ($param) {
                return number_format($param, 4, ".", "");
            }, $params) + ['crop' => 'focalpoint', 'fit' => 'crop'];
        }

        return [];
    }

    protected function getCropParams($crop_data)
    {
        $crop = json_decode($crop_data, true);

        if (!empty($crop)) {
            return array_map(function ($crop_coord) {
                return max(0, intval($crop_coord));
            }, $crop);
        }

        return [];
    }

    protected function getResourceId($name = 'resource_id')
    {
        $locale = $this->data['resource_locale'] ?? $this->locale;

        $locale = ($locale === "1" ? $this->locale : $locale);

        return $this->data[$name . '_' . $locale];
    }

    protected function view($block)
    {
        return view()->exists($this->customViewsNamespace . '.' . $block) ? $this->customViewsNamespace . '.' . $block : 'cms-toolkit::blocks.' . $block;
    }

}
