<?php

namespace A17\Twill\Repositories\Behaviors;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

trait HandleRevisions
{
    public function hydrateHandleRevisions($object, $fields)
    {
        // HandleRepeaters trait => getRepeaters
        foreach($this->getRepeaters() as $repeater) {
            $this->hydrateRepeater($object, $fields, $repeater['relation'], $repeater['model']);
        }

        // HandleBrowers trait => getBrowsers
        foreach($this->getBrowsers() as $browser) {
            $this->hydrateBrowser($object, $fields, $browser['relation'], $browser['positionAttribute'], $browser['model']);
        }

        return $object;
    }

    public function beforeSaveHandleRevisions($object, $fields)
    {
        $lastRevisionPayload = json_decode($object->revisions->first()->payload ?? "{}", true);

        if ($fields != $lastRevisionPayload) {
            $object->revisions()->create([
                'payload' => json_encode($fields),
                'user_id' => Auth::guard('twill_users')->user()->id ?? null,
            ]);
        }

        return $fields;
    }

    public function preview($id, $fields)
    {
        $object = $this->model->findOrFail($id);

        return $this->hydrateObject($object, $fields);
    }

    protected function hydrateObject($object, $fields)
    {
        $fields = $this->prepareFieldsBeforeSave($object, $fields);

        $object->fill(Arr::except($fields, $this->getReservedFields()));

        $object = $this->hydrate($object, $fields);

        return $object;
    }

    public function previewForRevision($id, $revisionId)
    {
        $object = $this->model->findOrFail($id);

        $fields = json_decode($object->revisions->where('id', $revisionId)->first()->payload, true);

        $hydratedObject = $this->hydrateObject($this->model->newInstance(), $fields);
        $hydratedObject->id = $id;

        return $hydratedObject;
    }

    public function hydrateMultiSelect($object, $fields, $relationship, $model = null, $customHydratedRelationship = null)
    {
        $fieldsHasElements = isset($fields[$relationship]) && !empty($fields[$relationship]);
        $relatedElements = $fieldsHasElements ? $fields[$relationship] : [];

        $relationRepository = $this->getModelRepository($relationship, $model);
        $relatedElementsCollection = Collection::make();

        foreach ($relatedElements as $relatedElement) {
            $newRelatedElement = $relationRepository->getById($relatedElement);
            $relatedElementsCollection->push($newRelatedElement);
        }

        $object->setRelation($customHydratedRelationship ?? $relationship, $relatedElementsCollection);
    }

    public function hydrateBrowser($object, $fields, $relationship, $positionAttribute = 'position', $model = null)
    {
        return $this->hydrateOrderedBelongsTomany($object, $fields, $relationship, $positionAttribute, $model);
    }

    public function hydrateOrderedBelongsTomany($object, $fields, $relationship, $positionAttribute = 'position', $model = null)
    {
        $fieldsHasElements = isset($fields['browsers'][$relationship]) && !empty($fields['browsers'][$relationship]);
        $relatedElements = $fieldsHasElements ? $fields['browsers'][$relationship] : [];

        $relationRepository = $this->getModelRepository($relationship, $model);
        $relatedElementsCollection = Collection::make();
        $position = 1;

        foreach ($relatedElements as $relatedElement) {
            $newRelatedElement = $relationRepository->getById($relatedElement['id']);
            $pivot = $newRelatedElement->newPivot($object, [$positionAttribute => $position++], $object->$relationship()->getTable(), true);
            $newRelatedElement->setRelation('pivot', $pivot);
            $relatedElementsCollection->push($newRelatedElement);
        }

        $object->setRelation($relationship, $relatedElementsCollection);
    }

    public function hydrateRepeater($object, $fields, $relationship, $model)
    {
        $relationFields = $fields['repeaters'][$relationship] ?? [];

        $relationRepository = $this->getModelRepository($relationship, $model);

        $repeaterCollection = Collection::make();

        foreach ($relationFields as $index => $relationField) {
            $relationField['position'] = $index + 1;
            $relationField[$this->model->getForeignKey()] = $object->id;
            unset($relationField['id']);

            $newRepeater = $relationRepository->createForPreview($relationField);

            $repeaterCollection->push($newRepeater);
        }

        $object->setRelation($relationship, $repeaterCollection);
    }

    public function getCountForMine()
    {
        $query = $this->model->newQuery();
        return $this->filter($query, $this->countScope)->mine()->count();
    }

    public function getCountByStatusSlugHandleRevisions($slug)
    {
        if ($slug === 'mine') {
            return $this->getCountForMine();
        }

        return false;
    }
}
