<?php

namespace App\Repositories\Behaviors;

trait HandleTypedTags
{
    public function addTypedTags($object, $fields, $relationship, $relationshipClass)
    {
        if (blank($fields[$relationship] ?? null)) {
            return;
        }

        $items = [];
        foreach ($fields[$relationship] as $tag) {
            $items[$tag] = ['tag_type' => $relationshipClass];
        }

        $object->$relationship()->wherePivot('tag_type', $relationshipClass)->sync($items);
    }
}
