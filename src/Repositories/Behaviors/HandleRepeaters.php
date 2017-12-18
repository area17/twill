<?php

namespace A17\CmsToolkit\Repositories\Behaviors;

use Carbon\Carbon;

trait HandleRepeaters
{
    public function updateRepeaterMany($object, $fields, $relation, $keepExisting = true, $model = null)
    {
        $relationFields = $fields['repeaters'][$relation] ?? [];
        $relationRepository = $this->getModelRepository($relation, $model);

        if (!$keepExisting) {
            $object->$relation()->each(function ($repeaterElement) {
                $repeaterElement->forceDelete();
            });
        }

        foreach ($relationFields as $relationField) {
            $newRelation = $relationRepository->create($relationField);
            $object->$relation()->attach($newRelation->id);
        }
    }

    public function updateRepeater($object, $fields, $relation, $model = null)
    {
        $relationFields = $fields['repeaters'][$relation] ?? [];

        $relationRepository = $this->getModelRepository($relation, $model);

        // if no relation field submitted, soft deletes all associated rows
        if (!$relationFields) {
            $relationRepository->updateBasic(null, [
                'deleted_at' => Carbon::now(),
            ], [
                $this->model->getForeignKey() => $object->id,
            ]);
        }

        // keep a list of updated and new rows to delete (soft delete?) old rows that were deleted from the frontend
        $currentIdList = [];

        foreach ($relationFields as $index => $relationField) {
            $relationField['position'] = $index + 1;
            if (isset($relationField['id']) && starts_with($relationField['id'], $relation)) {
                // row already exists, let's update
                $id = str_replace($relation . '-', '', $relationField['id']);
                $relationRepository->update($id, $relationField);
                $currentIdList[] = $id;
            } else {
                // new row, let's attach to our object and create
                $relationField[$this->model->getForeignKey()] = $object->id;
                unset($relationField['id']);
                $newRelation = $relationRepository->create($relationField);
                $currentIdList[] = $newRelation['id'];
            }
        }

        foreach ($object->$relation->pluck('id') as $id) {
            if (!in_array($id, $currentIdList)) {
                $relationRepository->updateBasic(null, [
                    'deleted_at' => Carbon::now(),
                ], [
                    'id' => $id,
                ]);
            }
        }
    }

    public function getFormFieldsForRepeater($object, $fields, $relation, $model = null)
    {
        $repeaters = [];
        $repeatersFields = [];
        $repeatersMedias = [];
        $relationRepository = $this->getModelRepository($relation, $model);
        $repeatersConfig = config('cms-toolkit.block_editor.repeaters');

        foreach ($object->$relation as $relationItem) {
            $repeaters[] = [
                'id' => $relation . '-' . $relationItem->id,
                'type' => $repeatersConfig[$relation]['component'],
                'title' => $repeatersConfig[$relation]['title'],
            ];

            $relatedItemFormFields = $relationRepository->getFormFields($relationItem);
            $translatedFields = [];

            if (isset($relatedItemFormFields['translations'])) {
                foreach ($relatedItemFormFields['translations'] as $key => $values) {
                    $repeatersFields[] = [
                        'name' => "blocks[$relation-$relationItem->id][$key]",
                        'value' => $values,
                    ];

                    $translatedFields[] = $key;
                }
            }

            if (isset($relatedItemFormFields['medias'])) {
                foreach ($relatedItemFormFields['medias'] as $key => $values) {
                    $repeatersMedias["blocks[$relation-$relationItem->id][$key]"] = $values;
                }
            }

            $itemFields = method_exists($relationItem, 'toRepeaterArray') ? $relationItem->toRepeaterArray() : array_except($relationItem->toArray(), $translatedFields);

            foreach ($itemFields as $key => $value) {
                $repeatersFields[] = [
                    'name' => "blocks[$relation-$relationItem->id][$key]",
                    'value' => $value,
                ];
            }
        }

        $fields['repeaters'][$relation] = $repeaters;

        $fields['repeaterFields'][$relation] = $repeatersFields;

        $fields['repeaterMedias'][$relation] = $repeatersMedias;

        return $fields;
    }

    private function getModelRepository($relation, $model = null)
    {
        if (!$model) {
            $model = ucfirst(str_singular($relation));
        }

        return app(config('cms-toolkit.namespace') . "\\Repositories\\" . ucfirst($model) . "Repository");
    }
}
