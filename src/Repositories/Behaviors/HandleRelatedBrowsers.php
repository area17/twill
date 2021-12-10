<?php

namespace A17\Twill\Repositories\Behaviors;

/**
 * Mimic HandleBrowsers, but when the relation uses
 * HasRelated instead of being a proper model relation.
 *
 * @see A17\Twill\Repositories\Behaviors\HandleBrowsers
 * @see https://github.com/area17/twill/discussions/940
 */
trait HandleRelatedBrowsers
{
    /**
     * All related browsers used in the model, as an array of browser names:
     * [
     *  'books',
     *  'publications'
     * ].
     *
     * When only the browser name is given here, its rest information will be inferred from the name.
     * Each browser's detail can also be overriden with an array
     * [
     *  'books',
     *  'publication' => [
     *      'relation' => 'magazine',
     *      'model' => 'Magazine'
     *  ]
     * ]
     *
     * @var string|array(array)|array(mix(string|array))
     */
    protected $relatedBrowsers = [];

    /**
     * @param \A17\Twill\Models\Model $object
     * @param array $fields
     * @return void
     */
    public function afterSaveHandleRelatedBrowsers($object, $fields)
    {
        foreach ($this->getRelatedBrowsers() as $browser) {
            $this->updateRelatedBrowser($object, $fields, $browser['browserName']);
        }
    }

    /**
     * @param \A17\Twill\Models\Model $object
     * @param array $fields
     * @return array
     */
    public function getFormFieldsHandleRelatedBrowsers($object, $fields)
    {
        foreach ($this->getRelatedBrowsers() as $browser) {
            $fields['browsers'][$browser['browserName']] = $this->getFormFieldsForRelatedBrowser($object, $browser['relation']);
        }

        return $fields;
    }

    /**
     * Get all related browser' detail info from the $relatedBrowsers attribute.
     * The missing information will be inferred by convention of Twill.
     *
     * @return Illuminate\Support\Collection
     */
    protected function getRelatedBrowsers()
    {
        return collect($this->relatedBrowsers)->map(function ($browser, $key) {
            $browserName = is_string($browser) ? $browser : $key;
            $moduleName = !empty($browser['moduleName']) ? $browser['moduleName'] : $this->inferModuleNameFromBrowserName($browserName);

            return [
                'relation' => !empty($browser['relation']) ? $browser['relation'] : $this->inferRelationFromBrowserName($browserName),
                'model' => !empty($browser['model']) ? $browser['model'] : $this->inferModelFromModuleName($moduleName),
                'browserName' => $browserName,
            ];
        })->values();
    }
}
