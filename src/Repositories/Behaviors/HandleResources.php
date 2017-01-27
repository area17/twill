<?php

namespace A17\CmsToolkit\Repositories\Behaviors;

trait HandleResources
{

    public function updateRelatedElements($object, $fields, $relatedFieldName)
    {
        $relatedElements = isset($fields[$relatedFieldName]) && !empty($fields[$relatedFieldName]) ? explode(',', $fields[$relatedFieldName]) : [];
        $relatedElementsWithPosition = [];
        $position = 1;
        foreach ($relatedElements as $relatedElement)
        {
            $relatedElementsWithPosition[$relatedElement] = ['position' => $position++];
        }

        $object->$relatedFieldName()->sync($relatedElementsWithPosition);
    }

}
