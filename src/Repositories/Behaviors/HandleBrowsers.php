<?php

namespace A17\Twill\Repositories\Behaviors;

trait HandleBrowsers
{
    /**
     * @param \A17\Twill\Models\Model $object
     * @param array $fields
     * @return void
     */
    public function afterSaveHandleBrowsers($object, $fields)
    {
        if (property_exists($this, 'browsers')) {
            foreach ($this->browsers as $moduleKey => $module) {
                if (is_string($module)) {
                    $this->updateBrowser($object, $fields, $module, 'position', $module);
                } elseif (is_array($module)) {
                    $browserName = !empty($module['browserName']) ? $module['browserName'] : $moduleKey;
                    $relation = !empty($module['relation']) ? $module['relation'] : $moduleKey;
                    $positionAttribute = !empty($module['positionAttribute']) ? $module['positionAttribute'] : 'position';
                    $this->updateBrowser($object, $fields, $relation, $positionAttribute, $browserName);
                }
            }
        }
    }

    /**
     * @param \A17\Twill\Models\Model $object
     * @param array $fields
     * @return array
     */
    public function getFormFieldsHandleBrowsers($object, $fields)
    {
        if (property_exists($this, 'browsers')) {
            foreach ($this->browsers as $moduleKey => $module) {
                if (is_string($module)) {
                    $fields['browsers'][$module] = $this->getFormFieldsForBrowser($object, $module, null, 'title', null);
                } elseif (is_array($module)) {
                    $relation = !empty($module['relation']) ? $module['relation'] : $moduleKey;
                    $routePrefix = isset($module['routePrefix']) ? $module['routePrefix'] : null;
                    $titleKey = !empty($module['titleKey']) ? $module['titleKey'] : 'title';
                    $moduleName = isset($module['moduleName']) ? $module['moduleName'] : null;
                    $browserName = !empty($module['browserName']) ? $module['browserName'] : $moduleKey;
                    $fields['browsers'][$browserName] = $this->getFormFieldsForBrowser($object, $relation, $routePrefix, $titleKey, $moduleName);
                }
            }
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
}
