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
        if (property_exists($this->model, 'browsers')) {
            foreach ($this->model->browsers as $moduleName) {
                $this->updateBrowser($object, $fields, $moduleName);
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
        if (property_exists($this->model, 'browsers')) {
            foreach ($this->model->browsers as $moduleName) {
                $fields['browsers'][$moduleName] = $this->getFormFieldsForBrowser($object, $moduleName);
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
    public function updateBrowser($object, $fields, $relationship, $positionAttribute = 'position')
    {
        $fieldsHasElements = isset($fields['browsers'][$relationship]) && !empty($fields['browsers'][$relationship]);
        $relatedElements = $fieldsHasElements ? $fields['browsers'][$relationship] : [];
        $relatedElementsWithPosition = [];
        $position = 1;
        foreach ($relatedElements as $relatedElement) {
            $relatedElementsWithPosition[$relatedElement['id']] = [$positionAttribute => $position++];
        }

        $object->$relationship()->sync($relatedElementsWithPosition);
    }
    
    /**
     * @param mixed $object
     * @param array $fields
     * @param string $browserName
     * @return void
     */
    public function updateRelatedBrowser($object, $fields, $browserName)
    {
        $object->sync($fields['browsers'][$browserName] ?? [], $browserName);
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
            ] + (($relatedElement->adminEditUrl ?? null) ? [] : [
                'edit' => $relatedElement->adminEditUrl,
            ]) + (classHasTrait($relatedElement, HasMedias::class) ? [
                'thumbnail' => $relatedElement->defaultCmsImage(['w' => 100, 'h' => 100]),
            ] : []) : [];
        })->reject(function ($item) {
            return empty($item);
        })->values()->toArray();
    }
}
