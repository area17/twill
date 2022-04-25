<?php

namespace A17\Twill\Repositories\Behaviors;

use A17\Twill\Models\Behaviors\HasRelated;
use A17\Twill\Models\RelatedItem;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

trait HandleRevisions
{
    /**
     * @return \A17\Twill\Models\Model
     * @param mixed[] $fields
     */
    public function hydrateHandleRevisions(\A17\Twill\Models\Model $object, array $fields)
    {
        foreach($this->getRepeaters() as $repeater) {
            $this->hydrateRepeater($object, $fields, $repeater['relation'], $repeater['model']);
        }

        foreach($this->getBrowsers() as $browser) {
            $this->hydrateBrowser($object, $fields, $browser['relation'], $browser['positionAttribute'], $browser['model']);
        }

        if (classHasTrait($object::class, HasRelated::class)) {
            $this->hydrateRelatedBrowsers($object, $fields);
        }

        return $object;
    }

    /**
     * @return array
     * @param mixed[] $fields
     */
    public function beforeSaveHandleRevisions(\A17\Twill\Models\Model $object, array $fields)
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

    /**
     * @return \A17\Twill\Models\Model
     * @param mixed[] $fields
     */
    public function preview(int $id, array $fields)
    {
        $object = $this->model->findOrFail($id);

        return $this->hydrateObject($object, $fields);
    }

    /**
     * @return \A17\Twill\Models\Model
     * @param mixed[] $fields
     */
    protected function hydrateObject(\A17\Twill\Models\Model $object, array $fields)
    {
        $fields = $this->prepareFieldsBeforeSave($object, $fields);

        $object->fill(Arr::except($fields, $this->getReservedFields()));

        return $this->hydrate($object, $fields);
    }

    /**
     * @return \A17\Twill\Models\Model
     */
    public function previewForRevision(int $id, int $revisionId)
    {
        $object = $this->model->findOrFail($id);

        $fields = json_decode($object->revisions->where('id', $revisionId)->first()->payload, true);

        $hydratedObject = $this->hydrateObject($this->model->newInstance(), $fields);
        $hydratedObject->id = $id;

        return $hydratedObject;
    }

    /**
     * @param \A17\Twill\Models\Model|null $model
     * @param string|null $customHydratedRelationship
     * @return void
     * @param mixed[] $fields
     */
    public function hydrateMultiSelect(\A17\Twill\Models\Model $object, array $fields, string $relationship, $model = null, $customHydratedRelationship = null)
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

    /**
     * @param \A17\Twill\Models\Model|null $model
     * @return null
     * @param mixed[] $fields
     */
    public function hydrateBrowser(\A17\Twill\Models\Model $object, array $fields, string $relationship, string $positionAttribute = 'position', $model = null)
    {
        return $this->hydrateOrderedBelongsToMany($object, $fields, $relationship, $positionAttribute, $model);
    }

    /**
     * @param \A17\Twill\Models\Model|null $model
     * @return void
     * @param mixed[] $fields
     */
    public function hydrateOrderedBelongsToMany(\A17\Twill\Models\Model $object, array $fields, string $relationship, string $positionAttribute = 'position', $model = null)
    {
        $fieldsHasElements = isset($fields['browsers'][$relationship]) && !empty($fields['browsers'][$relationship]);
        $relatedElements = $fieldsHasElements ? $fields['browsers'][$relationship] : [];

        $relationRepository = $this->getModelRepository($relationship, $model);
        $relatedElementsCollection = Collection::make();
        $position = 1;

        $tableName = $relationRepository->model->getTable();

        foreach ($relatedElements as $relatedElement) {
            $newRelatedElement = $relationRepository->getById($relatedElement['id']);
            $pivot = $newRelatedElement->newPivot($object, [$positionAttribute => $position++], $tableName, true);
            $newRelatedElement->setRelation('pivot', $pivot);
            $relatedElementsCollection->push($newRelatedElement);
        }

        $object->setRelation($relationship, $relatedElementsCollection);
    }

    /**
     * @return void
     * @param mixed[] $fields
     */
    public function hydrateRelatedBrowsers(\A17\Twill\Models\Model $object, array $fields)
    {
        $relatedBrowsers = $this->getRelatedBrowsers();

        $initialRelatedItems = $object->relatedItems()
            ->whereNotIn('browser_name', $relatedBrowsers->pluck('browserName'))
            ->get();

        $relatedBrowserItems = collect();

        foreach ($relatedBrowsers as $browser) {
            $browserField = $fields['browsers'][$browser['browserName']] ?? [];

            foreach ($browserField as $values) {
                $position = 1;

                $relatedBrowserItems->push(RelatedItem::make([
                    'subject_id' => $object->getKey(),
                    'subject_type' => $object->getMorphClass(),
                    'related_id' => $values['id'],
                    'related_type' => $values['endpointType'],
                    'browser_name' => $browser['browserName'],
                    'position' => $position++,
                ]));
            }
        }

        $allRelatedItems = $relatedBrowserItems->concat($initialRelatedItems);

        $object->setRelation('relatedItems', $allRelatedItems);
    }

    /**
     * @param string|null $repeaterName
     * @return void
     * @param mixed[] $fields
     */
    public function hydrateRepeater(\A17\Twill\Models\Model $object, array $fields, string $relationship, \A17\Twill\Models\Model $model, $repeaterName = null)
    {
        if (!$repeaterName) {
            $repeaterName = $relationship;
        }

        $relationFields = $fields['repeaters'][$repeaterName] ?? [];

        $relationRepository = getModelRepository($relationship, $model);

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

    /**
     * @return int
     */
    public function getCountForMine()
    {
        $query = $this->model->newQuery();
        return $this->filter($query, $this->countScope)->mine()->count();
    }

    public function getCountByStatusSlugHandleRevisions(string $slug): bool|int
    {
        if ($slug === 'mine') {
            return $this->getCountForMine();
        }

        return false;
    }
}
