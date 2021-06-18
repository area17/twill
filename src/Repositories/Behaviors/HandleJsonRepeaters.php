<?php

namespace A17\Twill\Repositories\Behaviors;

use A17\Twill\Services\Blocks\BlockCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 *
 * Save repeaters in a json column instead of a new model.
 *
 * This trait is not intended to replace main repeaters but to give a quick
 * and easy alternative for simple elements where creating a new table might be an overkill.
 *
 * Simply define an array with the repeater names on your repository:
 * protected $jsonRepeaters = [ 'REPEATER_NAME_1', 'REPEATER_NAME_2', ... ]
 *
 * Names must be the same as the ones you added in your `repeaters` attribute on `config\twill.php`
 * or the actual filename for self-contained repeaters introduced in 2.1.
 *
 * Supported: Input, WYSIWYG, textarea, browsers.
 * Not supported: Medias, Files, repeaters.
 *
 */

trait HandleJsonRepeaters
{

    /**
     * @param \A17\Twill\Models\Model $object
     * @param array $fields
     * @return string[]
     */
    public function prepareFieldsBeforeSaveHandleJsonRepeaters($object, $fields)
    {
        foreach ($this->jsonRepeaters as $repeater) {
            if (isset($fields['repeaters'][$repeater])) {
                $fields[$repeater] = $fields['repeaters'][$repeater];
            }
        }

        return $fields;
    }

    /**
     * @param \A17\Twill\Models\Model $object
     * @param array $fields
     * @return string[]
     */
    public function getFormFieldsHandleJsonRepeaters($object, $fields)
    {

        foreach ($this->jsonRepeaters as $repeater) {
            if (isset($fields[$repeater]) && !empty($fields[$repeater])) {
                $fields = $this->getJsonRepeater($fields, $repeater, $fields[$repeater]);
            }
        }

        return $fields;
    }

    public function getJsonRepeater($fields, $repeaterName, $serializedData)
    {
        $repeatersFields = [];
        $repeatersBrowsers = [];
        $repeatersList = app(BlockCollection::class)->getRepeaterList()->keyBy('name');

        foreach ($serializedData as $index => $repeaterItem) {
            $id = $repeaterItem['id'] ?? $index;

            $repeaters[] = [
                'id' => $id,
                'type' => $repeatersList[$repeaterName]['component'],
                'title' => $repeatersList[$repeaterName]['title'],
            ];

            if (isset($repeaterItem['browsers'])) {
                foreach ($repeaterItem['browsers'] as $key => $values) {
                    $repeatersBrowsers["blocks[$id][$key]"] = $values;
                }
            }

            $itemFields = Arr::except($repeaterItem, ['id', 'repeaters', 'files', 'medias', 'browsers', 'blocks']);

            foreach ($itemFields as $index => $value) {
                $repeatersFields[] = [
                    'name' => "blocks[$id][$index]",
                    'value' => $value,
                ];
            }
        }

        $fields['repeaters'][$repeaterName] = $repeaters;
        $fields['repeaterFields'][$repeaterName] = $repeatersFields;
        $fields['repeaterBrowsers'][$repeaterName] = $repeatersBrowsers;

        return $fields;
    }

}
