<?php

namespace A17\Twill\Repositories\Behaviors;

use Illuminate\Support\Str;

trait HandleBrowsers
{
    /**
     * All browsers used in the model, as an array of browser names: 
     * [
     *  'books',
     *  'publications'
     * ].
     * 
     * When only the browser name is given here, its rest information will be inferred from the name.
     * Each browser's detail can also be override with an array
     * [
     *  'books',
     *  'publication' => [
     *      'routePrefix' => 'collections',
     *      'titleKey' => 'name'
     *  ]
     * ]
     *
     * @var string|array(array)|array(mix(string|array))
     */
    protected $browsers = [];

    /**
     * @param \A17\Twill\Models\Model $object
     * @param array $fields
     * @return void
     */
    public function afterSaveHandleBrowsers($object, $fields)
    {
        foreach ($this->getBrowsers() as $browser) {
            $this->updateBrowser($object, $fields, $browser['relation'], $browser['positionAttribute'], $browser['browserName']);
        }
    }

    /**
     * @param \A17\Twill\Models\Model $object
     * @param array $fields
     * @return array
     */
    public function getFormFieldsHandleBrowsers($object, $fields)
    {
        foreach ($this->getBrowsers() as $browser) {
            $fields['browsers'][$browser['browserName']] = $this->getFormFieldsForBrowser($object, $browser['relation'], $browser['routePrefix'], $browser['titleKey'], $browser['moduleName']);
        }

        return $fields;
    }

    /**
     * @param \A17\Twill\Models\Model $object
     * @param array $fields
     * @param string $relationship
     * @param string $positionAttribute
     * @return void
     */
    public function updateBrowser($object, $fields, $relationship, $positionAttribute = 'position', $browserName = null)
    {
        $browserName = $browserName ?? $relationship;
        $fieldsHasElements = isset($fields['browsers'][$browserName]) && !empty($fields['browsers'][$browserName]);
        $relatedElements = $fieldsHasElements ? $fields['browsers'][$browserName] : [];
        $relatedElementsWithPosition = [];
        $position = 1;
        foreach ($relatedElements as $relatedElement) {
            $relatedElementsWithPosition[$relatedElement['id']] = [$positionAttribute => $position++];
        }

        $object->$relationship()->sync($relatedElementsWithPosition);
    }

    /**
     * @param \A17\Twill\Models\Model $object
     * @param array $fields
     * @param string $relationship
     * @param string $positionAttribute
     * @return void
     */
    public function updateOrderedBelongsTomany($object, $fields, $relationship, $positionAttribute = 'position')
    {
        $this->updateBrowser($object, $fields, $relationship, $positionAttribute);
    }

    /**
     * @param mixed $object
     * @param array $fields
     * @param string $browserName
     * @return void
     */
    public function updateRelatedBrowser($object, $fields, $browserName)
    {
        $object->saveRelated($fields['browsers'][$browserName] ?? [], $browserName);
    }

    /**
     * @param \A17\Twill\Models\Model $object
     * @param string $relation
     * @param string|null $routePrefix
     * @param string $titleKey
     * @param string|null $moduleName
     * @return array
     */
    public function getFormFieldsForBrowser($object, $relation, $routePrefix = null, $titleKey = 'title', $moduleName = null)
    {
        return $object->$relation->map(function ($relatedElement) use ($titleKey, $routePrefix, $relation, $moduleName) {
            return [
                'id' => $relatedElement->id,
                'name' => $relatedElement->titleInBrowser ?? $relatedElement->$titleKey,
                'edit' => moduleRoute($moduleName ?? $relation, $routePrefix ?? '', 'edit', $relatedElement->id),
                'endpointType' => $relatedElement->getMorphClass(),
            ] + (classHasTrait($relatedElement, HasMedias::class) ? [
                'thumbnail' => $relatedElement->defaultCmsImage(['w' => 100, 'h' => 100]),
            ] : []);
        })->toArray();
    }

    /**
     * @param \A17\Twill\Models\Model $object
     * @param string $relation
     * @return array
     */
    public function getFormFieldsForRelatedBrowser($object, $relation)
    {
        return $object->getRelated($relation)->map(function ($relatedElement) {
            return ($relatedElement != null) ? [
                'id' => $relatedElement->id,
                'name' => $relatedElement->titleInBrowser ?? $relatedElement->title,
                'endpointType' => $relatedElement->getMorphClass(),
            ] + (empty($relatedElement->adminEditUrl) ? [] : [
                'edit' => $relatedElement->adminEditUrl,
            ]) + (classHasTrait($relatedElement, HasMedias::class) ? [
                'thumbnail' => $relatedElement->defaultCmsImage(['w' => 100, 'h' => 100]),
            ] : []) : [];
        })->reject(function ($item) {
            return empty($item);
        })->values()->toArray();
    }

        /**
     * Get all browser' detail info from the $browsers attribute. 
     * The missing information will be inferred by convention of Twill.
     *
     * @return Illuminate\Support\Collection
     */
    protected function getBrowsers()
    {
        return collect($this->browsers)->map(function ($browser, $key) {
            $browserName = is_string($browser) ? $browser : $key;
            return [
                'relation' => !empty($browser['relation']) ? $browser['relation'] : $this->inferRelationFromBrowserName($browserName),
                'routePrefix' => isset($browser['routePrefix']) ? $browser['routePrefix'] : null,
                'titleKey' => !empty($browser['titleKey']) ? $browser['titleKey'] : 'title',
                'moduleName' => isset($browser['moduleName']) ? $browser['moduleName'] : null,
                'positionAttribute' => !empty($browser['positionAttribute']) ? $browser['positionAttribute'] : 'position',
                'browserName' => $browserName,
            ];
        })->values();
    }

    /**
     * The relation name shoud be lower camel case, ex. userGroup, contactOffice
     *
     * @param  string $browserName
     *
     * @return string
     */
    protected function inferRelationFromBrowserName(string $browserName): string
    {
        return Str::camel($browserName);
    }
}
