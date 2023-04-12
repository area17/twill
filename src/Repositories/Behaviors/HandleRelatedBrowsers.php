<?php

namespace A17\Twill\Repositories\Behaviors;

use A17\Twill\Models\Contracts\TwillModelContract;

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
     *      'titleKey' => 'name'
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
            $fields['browsers'][$browser['browserName']] = $this->getFormFieldsForRelatedBrowser(
                $object,
                $browser['relation'],
                $browser['titleKey']
            );
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
            $moduleName = empty($browser['moduleName']) ? $this->inferModuleNameFromBrowserName(
                $browserName
            ) : $browser['moduleName'];

            return [
                'relation' => empty($browser['relation']) ? $this->inferRelationFromBrowserName(
                    $browserName
                ) : $browser['relation'],
                'model' => empty($browser['model']) ? $this->inferModelFromModuleName($moduleName) : $browser['model'],
                'browserName' => $browserName,
                'titleKey' => $browser['titleKey'] ?? 'title',
            ];
        })->values();
    }

    /**
     * Called from afterReplicate in ModuleRepository.
     */
    public function afterDuplicateHandleRelatedBrowsers(TwillModelContract $old, TwillModelContract $new): void
    {
        foreach ($old->relatedItems?->groupBy('browser_name') ?? [] as $browserName => $group) {
            $new->saveRelated(
                $group->map(function ($item) {
                    return [
                        'id' => $item->related->id,
                        'endpointType' => $item->related->getMorphClass(),
                    ];
                })->toArray(),
                $browserName
            );
        }
    }
}
