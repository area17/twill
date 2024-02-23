<?php

namespace A17\Twill\Repositories\Behaviors;

use A17\Twill\Facades\TwillBlocks;
use A17\Twill\Facades\TwillUtil;
use A17\Twill\Models\Contracts\TwillModelContract;
use A17\Twill\Models\Model;
use A17\Twill\Repositories\ModuleRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

trait HandleRepeaters
{
    /**
     * All repeaters used in the model, as an array of repeater names:
     * [
     *     'article_repeater',
     *     'page_repeater'
     * ].
     *
     * When only the repeater name is given, the model and relation are inferred from the name.
     * The parameters can also be overridden with an array:
     * [
     *     'article_repeater',
     *     'page_repeater' => [
     *         'model' => 'Page',
     *         'relation' => 'pages'
     *     ]
     * ]
     *
     * @var array
     */
    protected array $repeaters = [];

    public function afterSaveHandleRepeaters(TwillModelContract $object, array $fields): void
    {
        foreach ($this->getRepeaters() as $repeater) {
            $this->updateRepeater(
                $object,
                $fields,
                $repeater['relation'],
                $repeater['model'],
                $repeater['repeaterName']
            );
        }
    }

    public function getFormFieldsHandleRepeaters(TwillModelContract $object, array $fields): array
    {
        foreach ($this->getRepeaters() as $repeater) {
            $fields = $this->getFormFieldsForRepeater(
                $object,
                $fields,
                $repeater['relation'],
                $repeater['model'],
                $repeater['repeaterName']
            );
        }

        return $fields;
    }

    /**
     * @deprecated use updateRepeaterWithPivot
     */
    public function updateRepeaterMany(
        TwillModelContract $object,
        array $fields,
        string $relation,
        bool $keepExisting = true,
        ?string $model = null
    ): void {
        $relationFields = $fields['repeaters'][$relation] ?? [];
        $relationRepository = getModelRepository($relation, $model);

        if (! $keepExisting) {
            $object->$relation()->each(function ($repeaterElement) {
                $repeaterElement->forceDelete();
            });
        }

        foreach ($relationFields as $relationField) {
            $newRelation = $relationRepository->create($relationField);
            $object->$relation()->attach($newRelation->id);
        }
    }

    public function updateRepeaterMorphMany(
        TwillModelContract $object,
        array $fields,
        string $relation,
        ?string $morph = null,
        ?string $model = null,
        ?string $repeaterName = null
    ): void {
        if (! $repeaterName) {
            $repeaterName = $relation;
        }

        $relationFields = $fields['repeaters'][$repeaterName] ?? [];
        $relationRepository = $this->getModelRepository($relation, $model);

        $morph = $morph ?: $relation;

        $morphFieldType = $morph . '_type';
        $morphFieldId = $morph . '_id';

        // if no relation field submitted, soft deletes all associated rows
        if (! $relationFields) {
            $relationRepository->updateBasic(null, [
                'deleted_at' => Carbon::now(),
            ], [
                $morphFieldType => $object->getMorphClass(),
                $morphFieldId => $object->id,
            ]);
        }

        // keep a list of updated and new rows to delete (soft delete?) old rows that were deleted from the frontend
        $currentIdList = [];

        // @todo: This needs refactoring in 3.x
        foreach ($relationFields as $index => $relationField) {
            $relationField['position'] = $index + 1;
            $relationField[$morphFieldId] = $object->id;
            $relationField[$morphFieldType] = $object->getMorphClass();

            if (isset($relationField['id']) && Str::startsWith($relationField['id'], $relation)) {
                // row already exists, let's update
                $id = str_replace($relation . '-', '', $relationField['id']);
                $relationRepository->update($id, $relationField);
                $currentIdList[] = (int)$id;
            } else {
                // new row, let's attach to our object and create
                unset($relationField['id']);
                $newRelation = $relationRepository->create($relationField);
                $object->$relation()->save($newRelation);
                $currentIdList[] = (int)$newRelation['id'];
            }
        }

        foreach ($object->$relation()->pluck('id') as $id) {
            if (! in_array($id, $currentIdList, true)) {
                $relationRepository->updateBasic(null, [
                    'deleted_at' => Carbon::now(),
                ], [
                    'id' => $id,
                ]);
            }
        }
    }

    public function updateRepeaterWithPivot(
        TwillModelContract $object,
        array $fields,
        string $relation,
        array $pivotFields,
        ?string $modelOrRepository = null,
        ?string $repeaterName = null,
    ): void {
        if (! $repeaterName) {
            $repeaterName = $relation;
        }

        $relationFields = $fields['repeaters'][$repeaterName] ?? [];

        $relationRepository = $this->getModelRepository($relation, $modelOrRepository);

        // If no relation field submitted, soft deletes all associated rows.
        // We only do this when the model is already existing.
        if (! $relationFields && ! $object->wasRecentlyCreated) {
            $object->{$relation}()->detach();
        }

        // Add the position to the pivot fields.
        $pivotFields[] = 'position';

        // Keep a list of updated and new rows to delete (soft delete?) old rows that were deleted from the frontend
        // This list contains the ID's of the relation table rather than that of the target model!
        $currentIdList = [];

        /** @var Collection<Model> $currentRelations */
        $currentRelations = $object->{$relation}()->withPivot('id')->get();

        foreach ($relationFields as $index => $relationField) {
            $relationField['position'] = $index + 1;

            // If the relation is not an "existing" one try to match it with our session.
            if (
                ! Str::startsWith($relationField['id'], $relation) &&
                $pivotRowId = TwillUtil::hasRepeaterIdFor($relationField['id'])
            ) {
                $relationField['id'] = $relation . '-' . $pivotRowId;
            }

            // Set the active data based on the parent.
            if (! isset($relationField['languages']) && isset($relationField['active'])) {
                foreach (array_keys($relationField['active']) as $langCode) {
                    // Add the languages field.
                    $relationField['languages'][] = [
                        'value' => $langCode,
                        'published' => $fields[$langCode]['active'],
                    ];
                }
            }

            if (isset($relationField['id']) && Str::startsWith($relationField['id'], $relation)) {
                // row already exists, let's update, the $id is the id in the pivot table.
                $pivotRowId = str_replace($relation . '-', '', $relationField['id']);

                // The id here is the one of the pivot column. From there we can update the correct target.
                $currentRelation = $currentRelations->first(function (Model $model) use ($pivotRowId) {
                    return (int)$pivotRowId === $model->pivot->id;
                });

                $relationRepository->update($currentRelation->id, $relationField);

                $pivotFieldData = $this->encodePivotFields(collect($relationField)->only($pivotFields)->all());
                if (! empty($pivotFieldData)) {
                    $currentRelation->pivot->update($pivotFieldData);
                }

                $currentIdList[] = (int)$pivotRowId;
            } else {
                $frontEndId = $relationField['id'];
                if ($relationField['repeater_target_id'] ?? false) {
                    // If the repeater_target_id is set we use that to create a new record based of an existing entity.
                    $newRelation = $relationRepository->findOrFail($relationField['repeater_target_id']);
                    // Update the target.
                    $relationRepository->update($relationField['repeater_target_id'], $relationField);
                    unset($relationField['repeater_target_id']);
                } else {
                    // new row, let's attach to our object and create
                    $relationField[$this->model->getForeignKey()] = $object->id;
                    unset($relationField['id']);
                    $newRelation = $relationRepository->create($relationField);
                }

                $currentIdList[] = (int)$newRelation['id'];

                $pivotFieldData = $this->encodePivotFields(collect($relationField)->only($pivotFields)->all());

                $object->{$relation}()->attach($newRelation['id'], $pivotFieldData);

                $latestAttached = $object->{$relation}()->withPivot('id')->orderByPivot('id', 'desc')->get()->last();

                TwillUtil::registerRepeaterId($frontEndId, $latestAttached->pivot->id);
            }
        }

        $current = $object->{$relation}()->withPivot('id')->get();
        if ($current->isNotEmpty()) {
            foreach ($current as $existingRelation) {
                if (! in_array((int)$existingRelation->pivot->id, $currentIdList, true)) {
                    // The pivot table is treated differently.
                    $object->{$relation}()->detach($existingRelation->pivot->id);
                }
            }
        }
    }

    /**
     * Given relation, model and repeaterName, retrieve the repeater data from request and update the database record.
     */
    public function updateRepeater(
        TwillModelContract $object,
        array $fields,
        string $relation,
        null|string|TwillModelContract|ModuleRepository $modelOrRepository = null,
        ?string $repeaterName = null
    ): void {
        if (! $repeaterName) {
            $repeaterName = $relation;
        }

        $relationFields = $fields['repeaters'][$repeaterName] ?? [];

        $relationRepository = $this->getModelRepository($relation, $modelOrRepository);

        if (method_exists($this->model, $relation)) {
            /** @var Relation $relationInstance */
            $relationInstance = $this->model->$relation();
            if ($relationInstance instanceof BelongsTo || $relationInstance instanceof HasOneOrMany) {
                $fk = $relationInstance->getForeignKeyName();
            }
        }
        $fk ??= $this->model->getForeignKey();

        // If no relation field submitted, soft deletes all associated rows.
        // We only do this when the model is already existing.
        if (! $relationFields && ! $object->wasRecentlyCreated) {
            $relationRepository->updateBasic(null, [
                'deleted_at' => Carbon::now(),
            ], [
                $fk => $object->id,
            ]);
        }

        // keep a list of updated and new rows to delete (soft delete?) old rows that were deleted from the frontend
        $currentIdList = [];

        foreach ($relationFields as $index => $relationField) {
            $relationField['position'] = $index + 1;
            // If the relation is not an "existing" one try to match it with our session.
            if (
                ! Str::startsWith($relationField['id'], $relation) &&
                $id = TwillUtil::hasRepeaterIdFor($relationField['id'])
            ) {
                $relationField['id'] = $relation . '-' . $id;
            }

            // Set the active data based on the parent.
            if (! isset($relationField['languages']) && isset($relationField['active'])) {
                foreach (array_keys($relationField['active']) as $langCode) {
                    // Add the languages field.
                    $relationField['languages'][] = [
                        'value' => $langCode,
                        'published' => $fields[$langCode]['active'],
                    ];
                }
            }

            if (isset($relationField['id']) && Str::startsWith($relationField['id'], $relation)) {
                // row already exists, let's update
                $id = str_replace($relation . '-', '', $relationField['id']);
                $relationRepository->update($id, $relationField);

                $currentIdList[] = (int)$id;
            } else {
                // new row, let's attach to our object and create
                $relationField[$fk] = $object->id;
                $frontEndId = $relationField['id'];
                unset($relationField['id']);
                $newRelation = $relationRepository->create($relationField);
                $currentIdList[] = (int)$newRelation['id'];

                TwillUtil::registerRepeaterId($frontEndId, $newRelation->id);
            }
        }

        foreach ($object->{$relation}()->pluck('id') as $id) {
            if (! in_array($id, $currentIdList, true)) {
                // The pivot table is treated differently.
                $relationRepository->updateBasic(null, [
                    'deleted_at' => Carbon::now(),
                ], [
                    'id' => $id,
                ]);
            }
        }
    }

    /**
     * This makes sure that arrays are json encode (translations).
     */
    private function encodePivotFields(array $fields): array
    {
        foreach ($fields as $key => $pivotField) {
            if (is_array($pivotField)) {
                $fields[$key] = json_encode($pivotField);
            }
        }

        return $fields;
    }

    public function getFormFieldForRepeaterWithPivot(
        TwillModelContract $object,
        array $fields,
        string $relation,
        array $pivotFields,
        null|string|TwillModelContract|ModuleRepository $modelOrRepository = null,
        ?string $repeaterName = null
    ): array {
        return $this->getFormFieldsShared($object, $fields, $relation, $pivotFields, $modelOrRepository, $repeaterName);
    }

    private function getFormFieldsShared(
        TwillModelContract $object,
        array $fields,
        string $relation,
        array $pivotFields,
        null|string|TwillModelContract|ModuleRepository $modelOrRepository = null,
        ?string $repeaterName = null
    ): array {
        if (! $repeaterName) {
            $repeaterName = $relation;
        }

        $repeaters = [];
        $repeatersFields = [];
        $repeatersBrowsers = [];
        $repeatersMedias = [];
        $repeatersFiles = [];
        $relationRepository = $this->getModelRepository($relation, $modelOrRepository);

        $repeaterType = TwillBlocks::findRepeaterByName($repeaterName);

        if (!empty($pivotFields)) {
            $pivotFields[] = 'id';
            $objects = $object->$relation()->withPivot($pivotFields)->get();
        } else {
            $objects = $object->$relation;
        }

        foreach ($objects as $relationItem) {
            $pivotRowId = !empty($pivotFields) ? $relationItem->pivot->id : $relationItem->id;
            $repeaters[] = [
                'id' => $relation . '-' . $pivotRowId,
                'type' => $repeaterType->component,
                'title' => $repeaterType->title,
                'titleField' => $repeaterType->titleField,
                'hideTitlePrefix' => $repeaterType->hideTitlePrefix,
            ];

            $relatedItemFormFields = $relationRepository->getFormFields($relationItem);
            $translatedFields = [];

            if (isset($relatedItemFormFields['translations'])) {
                foreach ($relatedItemFormFields['translations'] as $key => $values) {
                    $repeatersFields[] = [
                        'name' => "blocks[$relation-$pivotRowId][$key]",
                        'value' => $values,
                    ];

                    $translatedFields[] = $key;
                }
            }

            if (isset($relatedItemFormFields['medias'])) {
                if (config('twill.media_library.translated_form_fields', false)) {
                    Collection::make($relatedItemFormFields['medias'])->each(
                        function ($rolesWithMedias, $locale) use (&$repeatersMedias, $relation, $relationItem) {
                            $repeatersMedias[] = Collection::make($rolesWithMedias)->mapWithKeys(
                                function ($medias, $role) use ($locale, $relation, $relationItem) {
                                    return [
                                        "blocks[$relation-$relationItem->id][$role][$locale]" => $medias,
                                    ];
                                }
                            )->toArray();
                        }
                    );
                } else {
                    foreach ($relatedItemFormFields['medias'] as $key => $values) {
                        $repeatersMedias["blocks[$relation-$relationItem->id][$key]"] = $values;
                    }
                }
            }

            if (isset($relatedItemFormFields['files'])) {
                Collection::make($relatedItemFormFields['files'])->each(
                    function ($rolesWithFiles, $locale) use (&$repeatersFiles, $relation, $relationItem) {
                        $repeatersFiles[] = Collection::make($rolesWithFiles)->mapWithKeys(
                            function ($files, $role) use ($locale, $relation, $relationItem) {
                                return [
                                    "blocks[$relation-$relationItem->id][$role][$locale]" => $files,
                                ];
                            }
                        )->toArray();
                    }
                );
            }

            if (isset($relatedItemFormFields['browsers'])) {
                foreach ($relatedItemFormFields['browsers'] as $key => $values) {
                    $repeatersBrowsers["blocks[$relation-$relationItem->id][$key]"] = $values;
                }
            }

            $itemFields = method_exists($relationItem, 'toRepeaterArray') ?
                $relationItem->toRepeaterArray() :
                Arr::except($relationItem->attributesToArray(), $translatedFields);

            foreach ($pivotFields as $pivotField) {
                if ($pivotField === 'id') {
                    continue;
                }

                $itemFields[$pivotField] = $this->decodePivotField($relationItem->pivot->{$pivotField} ?? null);
            }

            foreach ($itemFields as $key => $value) {
                $repeatersFields[] = [
                    'name' => "blocks[$relation-$pivotRowId][$key]",
                    'value' => $value,
                ];
            }

            foreach ($relatedItemFormFields['blocks'] ?? [] as $key => $block) {
                $fields['blocks'][str_contains($key, '|') ? $key : "blocks-$relation-{$relationItem->id}|$key"] = $block;
            }
            $fields['blocksFields'] = array_merge($fields['blocksFields'] ?? [], $relatedItemFormFields['blocksFields'] ?? []);

            if (isset($relatedItemFormFields['repeaters'])) {
                foreach ($relatedItemFormFields['repeaters'] as $childRepeaterName => $childRepeaterItems) {
                    if (str_contains($childRepeaterName, '|')) {
                        $fields['repeaters'][$childRepeaterName] = $childRepeaterItems;
                        continue;
                    }
                    $fields['repeaters']["blocks-$relation-{$relationItem->id}|$childRepeaterName"] = $childRepeaterItems;
                    $repeatersFields = array_merge(
                        $repeatersFields,
                        $relatedItemFormFields['repeaterFields'][$childRepeaterName] ?? []
                    );
                    $repeatersMedias = array_merge(
                        $repeatersMedias,
                        $relatedItemFormFields['repeaterMedias'][$childRepeaterName] ?? []
                    );
                    $repeatersFiles = array_merge(
                        $repeatersFiles,
                        $relatedItemFormFields['repeaterFiles'][$childRepeaterName] ?? []
                    );
                    $repeatersBrowsers = array_merge(
                        $repeatersBrowsers,
                        $relatedItemFormFields['repeaterBrowsers'][$childRepeaterName] ?? []
                    );
                }
            }
        }

        if (! empty($repeatersMedias) && config('twill.media_library.translated_form_fields', false)) {
            $repeatersMedias = array_merge(...$repeatersMedias);
        }

        if (! empty($repeatersFiles)) {
            $repeatersFiles = array_merge(...$repeatersFiles);
        }

        $fields['repeaters'][$repeaterName] = $repeaters;
        $fields['repeaterFields'][$repeaterName] = $repeatersFields;
        $fields['repeaterMedias'][$repeaterName] = $repeatersMedias;
        $fields['repeaterFiles'][$repeaterName] = $repeatersFiles;
        $fields['repeaterBrowsers'][$repeaterName] = $repeatersBrowsers;

        return $fields;
    }

    /**
     * Given relation, model and repeaterName, get the necessary fields for rendering a repeater.
     */
    public function getFormFieldsForRepeater(
        TwillModelContract $object,
        array $fields,
        string $relation,
        null|string|TwillModelContract|ModuleRepository $modelOrRepository = null,
        ?string $repeaterName = null
    ): array {
        return $this->getFormFieldsShared($object, $fields, $relation, [], $modelOrRepository, $repeaterName);
    }

    private function decodePivotField(?string $data): null|array|string
    {
        if (! $data) {
            return null;
        }

        try {
            return json_decode($data, true, 512, JSON_THROW_ON_ERROR);
        } catch (Exception) {
            return $data;
        }
    }

    /**
     * Get all repeaters' model and relation from the $repeaters attribute.
     * The missing information will be inferred by convention of Twill.
     */
    protected function getRepeaters(): Collection
    {
        return collect($this->repeaters)->map(function ($repeater, $key) {
            $repeaterName = is_string($repeater) ? $repeater : $key;

            return [
                'relation' => empty($repeater['relation']) ? $this->inferRelationFromRepeaterName(
                    $repeaterName
                ) : $repeater['relation'],
                'model' => empty($repeater['model']) ? $this->inferModelFromRepeaterName(
                    $repeaterName
                ) : $repeater['model'],
                'repeaterName' => $repeaterName,
            ];
        })->values();
    }

    /**
     * Guess the relation name (should be lower camel case, ex. userGroup, contactOffice).
     */
    protected function inferRelationFromRepeaterName(string $repeaterName): string
    {
        return Str::camel($repeaterName);
    }

    /**
     * Guess the model name (should be singular upper camel case, ex. User, ArticleType).
     */
    protected function inferModelFromRepeaterName(string $repeaterName): string
    {
        return Str::studly(Str::singular($repeaterName));
    }
}
