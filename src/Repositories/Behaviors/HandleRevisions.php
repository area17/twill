<?php

namespace A17\Twill\Repositories\Behaviors;

use A17\Twill\Facades\TwillConfig;
use A17\Twill\Models\Behaviors\HasRelated;
use A17\Twill\Models\Contracts\TwillModelContract;
use A17\Twill\Models\RelatedItem;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use A17\Twill\Jobs\CleanupRevisions;

trait HandleRevisions
{
    /**
     * The Laravel queue name to be used for the revision limiting.
     */
    protected string $revisionLimitJobQueue = 'default';

    public function hydrateHandleRevisions(TwillModelContract $object, array $fields): TwillModelContract
    {
        foreach ($this->getRepeaters() as $repeater) {
            $this->hydrateRepeater($object, $fields, $repeater['relation'], $repeater['model']);
        }

        foreach ($this->getBrowsers() as $browser) {
            $this->hydrateBrowser($object, $fields, $browser['relation'], $browser['positionAttribute'], $browser['model']);
        }

        if (classHasTrait(get_class($object), HasRelated::class)) {
            $this->hydrateRelatedBrowsers($object, $fields);
        }

        return $object;
    }

    public function afterSaveOriginalDataHandleRevisions(TwillModelContract $object, array $fields): array
    {
        $this->createRevisionIfNeeded($object, $fields);

        return $fields;
    }

    public function createRevisionIfNeeded(TwillModelContract $object, array $fields): array
    {
        $lastRevisionPayload = json_decode($object->revisions->first()->payload ?? "{}", true);

        if ($fields !== $lastRevisionPayload) {
            $object->revisions()->create([
                'payload' => json_encode($fields),
                'user_id' => Auth::guard('twill_users')->user()->id ?? null,
            ]);
        }

        if (isset($object->limitRevisions) || TwillConfig::getRevisionLimit()) {
            CleanupRevisions::dispatch($object)
                ->onQueue($this->revisionLimitJobQueue);
        }

        return $fields;
    }

    public function preview(int $id, array $fields): TwillModelContract
    {
        $object = $this->model->findOrFail($id);

        return $this->hydrateObject($object, $fields);
    }

    protected function hydrateObject(TwillModelContract $object, array $fields): TwillModelContract
    {
        $fields = $this->prepareFieldsBeforeSave($object, $fields);

        $object->fill(Arr::except($fields, $this->getReservedFields()));

        return $this->hydrate($object, $fields);
    }

    public function previewForRevision(int $id, int $revisionId): TwillModelContract
    {
        $object = $this->model->findOrFail($id);

        $fields = json_decode($object->revisions->where('id', $revisionId)->first()->payload, true);

        $hydratedObject = $this->hydrateObject($this->model->newInstance(), $fields);
        $hydratedObject->id = $id;

        return $hydratedObject;
    }

    public function hydrateMultiSelect(
        TwillModelContract $object,
        array $fields,
        string $relationship,
        null|string|TwillModelContract $model = null,
        ?string $customHydratedRelationship = null
    ): void {
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

    public function hydrateBrowser(
        TwillModelContract $object,
        array $fields,
        string $relationship,
        string $positionAttribute = 'position',
        null|TwillModelContract|string $model = null
    ): void {
        $this->hydrateOrderedBelongsToMany($object, $fields, $relationship, $positionAttribute, $model);
    }

    public function hydrateOrderedBelongsToMany(
        TwillModelContract $object,
        array $fields,
        string $relationship,
        string $positionAttribute = 'position',
        null|TwillModelContract|string $model = null
    ): void {
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

    public function hydrateRelatedBrowsers(TwillModelContract $object, array $fields): void
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

    public function hydrateRepeater(
        TwillModelContract $object,
        array $fields,
        string $relationship,
        string $model,
        ?string $repeaterName = null
    ): void {
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

    public function getCountForMine(): int
    {
        $query = $this->model->newQuery();
        return $this->filter($query, $this->countScope)->mine()->count();
    }

    public function getCountByStatusSlugHandleRevisions(string $slug): int|bool
    {
        if ($slug === 'mine') {
            return $this->getCountForMine();
        }

        return false;
    }
}
