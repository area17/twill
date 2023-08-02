<?php

namespace A17\Twill\Repositories\Behaviors;

use A17\Twill\Facades\TwillBlocks;
use Illuminate\Support\Arr;

/**
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
 */
trait HandleJsonRepeaters
{
    /**
     * @param array $fields
     * @return array
     */
    public function prepareFieldsBeforeCreateHandleJsonRepeaters($fields)
    {
        foreach ($this->jsonRepeaters as $repeater) {
            if (isset($fields['repeaters'][$repeater])) {
                $fields[$repeater] = $fields['repeaters'][$repeater];
            }
        }

        return $fields;
    }

    /**
     * @param \A17\Twill\Models\Model|null $object
     * @param array $fields
     * @return array
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
     * @param \A17\Twill\Models\Model|null $object
     * @param array $fields
     * @return array
     */
    public function getFormFieldsHandleJsonRepeaters($object, $fields)
    {
        foreach ($this->jsonRepeaters as $repeater) {
            if (isset($fields[$repeater]) && ! empty($fields[$repeater])) {
                $fields = $this->getJsonRepeater($fields, $repeater, $fields[$repeater]);
            }
        }

        return $fields;
    }

    public function getJsonRepeater(array $fields, string $repeaterName, array $serializedData): array
    {
        $repeatersFields = [];
        $repeatersBrowsers = [];
        /** @var \A17\Twill\Services\Blocks\Block[] $repeatersList */
        $repeatersList = TwillBlocks::getRepeaters()->keyBy('name');
        $repeaters = [];

        foreach ($serializedData as $index => $repeaterItem) {
            $id = $repeaterItem['id'] ?? $index;

            $repeater = $repeatersList[$repeaterName] ?? $repeatersList['dynamic-repeater-' . $repeaterName] ?? null;

            if (!$repeater) {
                // There is no repeater found. This can be due to code removal but a database left-over.
                // In that case, we cannot do anything so we simply return the fields.
                return $fields;
            }

            $repeaters[] = [
                'id' => $id,
                'type' => $repeater->component,
                'title' => $repeater->title,
                'titleField' => $repeater->titleField,
                'hideTitlePrefix' => $repeater->hideTitlePrefix,
            ];

            if (isset($repeaterItem['browsers'])) {
                foreach ($repeaterItem['browsers'] as $key => $values) {
                    $repeatersBrowsers["blocks[$id][$key]"] = $values;
                }
            }

            $itemFields = Arr::except($repeaterItem, ['id', 'repeaters', 'files', 'medias', 'browsers', 'blocks']);

            foreach ($itemFields as $itemFieldIndex => $value) {
                $repeatersFields[] = [
                    'name' => "blocks[$id][$itemFieldIndex]",
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
