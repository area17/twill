<?php

namespace A17\Twill\Repositories\Behaviors;

use A17\Twill\Facades\TwillBlocks;
use A17\Twill\Facades\TwillUtil;
use Carbon\Carbon;
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
    protected $repeaters = [];

    /**
     * @return void
     * @param mixed[] $fields
     */
    public function afterSaveHandleRepeaters(\A17\Twill\Models\Model $object, array $fields)
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

    /**
     * @return array
     * @param mixed[] $fields
     */
    public function getFormFieldsHandleRepeaters(\A17\Twill\Models\Model $object, array $fields)
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
     * @param \A17\Twill\Models\Model|null $model
     * @return void
     * @param mixed[] $fields
     */
    public function updateRepeaterMany(\A17\Twill\Models\Model $object, array $fields, string $relation, bool $keepExisting = true, $model = null)
    {
        $relationFields = $fields['repeaters'][$relation] ?? [];
        $relationRepository = getModelRepository($relation, $model);

        if (! $keepExisting) {
            $object->$relation()->each(function ($repeaterElement): void {
                $repeaterElement->forceDelete();
            });
        }

        foreach ($relationFields as $relationField) {
            $newRelation = $relationRepository->create($relationField);
            $object->$relation()->attach($newRelation->id);
        }
    }

    /**
     * @param string|null $morph
     * @param \A17\Twill\Models\Model|null $model
     * @param string|null $repeaterName
     * @return void
     * @param mixed[] $fields
     */
    public function updateRepeaterMorphMany(
        \A17\Twill\Models\Model $object,
        array $fields,
        string $relation,
        $morph = null,
        $model = null,
        $repeaterName = null
    ) {
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
                $currentIdList[] = $id;
            } else {
                // new row, let's attach to our object and create
                unset($relationField['id']);
                $newRelation = $relationRepository->create($relationField);
                $object->$relation()->save($newRelation);
                $currentIdList[] = $newRelation['id'];
            }
        }

        foreach ($object->$relation()->pluck('id') as $id) {
            if (!in_array($id, $currentIdList)) {
                $relationRepository->updateBasic(null, [
                    'deleted_at' => Carbon::now(),
                ], [
                    'id' => $id,
                ]);
            }
        }
    }

    /**
     * Given relation, model and repeaterName, retrieve the repeater data from request and update the database record.
     *
     * @param \A17\Twill\Models\Model|\A17\Twill\Repositories\ModuleRepository|null $modelOrRepository
     * @param string|null $repeaterName
     * @return void
     * @param mixed[] $fields
     */
    public function updateRepeater(\A17\Twill\Models\Model $object, array $fields, string $relation, $modelOrRepository = null, $repeaterName = null)
    {
        if (! $repeaterName) {
            $repeaterName = $relation;
        }

        $relationFields = $fields['repeaters'][$repeaterName] ?? [];

        $relationRepository = $this->getModelRepository($relation, $modelOrRepository);

        // if no relation field submitted, soft deletes all associated rows
        if (! $relationFields) {
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
            // If the relation is not an "existing" one try to match it with our session.
            if (
                ! Str::startsWith($relationField['id'], $relation) &&
                $id = TwillUtil::hasRepeaterIdFor($relationField['id'])
            ) {
                $relationField['id'] = $relation . '-' . $id;
            }

            // Set the active data based on the parent.
            if (! isset($relationField['languages']) && isset($relationField['active'])) {
                foreach ($relationField['active'] as $langCode => $active) {
                    // Add the languages field.
                    $relationField['languages'][] = [
                        'value' => $langCode,
                        'published' => $fields[$langCode]['active'],
                    ];
                }
            }

            // Finally store the data.
            if (isset($relationField['id']) && Str::startsWith($relationField['id'], $relation)) {
                // row already exists, let's update
                $id = str_replace($relation . '-', '', $relationField['id']);
                $relationRepository->update($id, $relationField);
                $currentIdList[] = $id;
            } else {
                // new row, let's attach to our object and create
                $relationField[$this->model->getForeignKey()] = $object->id;
                $frontEndId = $relationField['id'];
                unset($relationField['id']);
                $newRelation = $relationRepository->create($relationField);
                $currentIdList[] = $newRelation['id'];

                TwillUtil::registerRepeaterId($frontEndId, $newRelation->id);
            }
        }

        foreach ($object->getRelation()->pluck('id') as $id) {
            if (! in_array($id, $currentIdList)) {
                $relationRepository->updateBasic(null, [
                    'deleted_at' => Carbon::now(),
                ], [
                    'id' => $id,
                ]);
            }
        }
    }

    /**
     * Given relation, model and repeaterName, get the necessary fields for rendering a repeater.
     *
     * @param \A17\Twill\Models\Model|\A17\Twill\Repositories\ModuleRepository|null $modelOrRepository
     * @param string|null $repeaterName
     * @return array
     * @param mixed[] $fields
     */
    public function getFormFieldsForRepeater(
        \A17\Twill\Models\Model $object,
        array $fields,
        string $relation,
        $modelOrRepository = null,
        $repeaterName = null
    ) {
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

        foreach ($object->$relation as $relationItem) {
            $repeaters[] = [
                'id' => $relation . '-' . $relationItem->id,
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
                        'name' => sprintf('blocks[%s-%s][%s]', $relation, $relationItem->id, $key),
                        'value' => $values,
                    ];

                    $translatedFields[] = $key;
                }
            }

            if (isset($relatedItemFormFields['medias'])) {
                if (config('twill.media_library.translated_form_fields', false)) {
                    Collection::make($relatedItemFormFields['medias'])->each(
                        function ($rolesWithMedias, $locale) use (&$repeatersMedias, $relation, $relationItem): void {
                            $repeatersMedias[] = Collection::make($rolesWithMedias)->mapWithKeys(
                                function ($medias, $role) use ($locale, $relation, $relationItem): array {
                                    return [
                                        sprintf('blocks[%s-%s][%s][%s]', $relation, $relationItem->id, $role, $locale) => $medias,
                                    ];
                                }
                            )->toArray();
                        }
                    );
                } else {
                    foreach ($relatedItemFormFields['medias'] as $key => $values) {
                        $repeatersMedias[sprintf('blocks[%s-%s][%s]', $relation, $relationItem->id, $key)] = $values;
                    }
                }
            }

            if (isset($relatedItemFormFields['files'])) {
                Collection::make($relatedItemFormFields['files'])->each(
                    function ($rolesWithFiles, $locale) use (&$repeatersFiles, $relation, $relationItem): void {
                        $repeatersFiles[] = Collection::make($rolesWithFiles)->mapWithKeys(
                            function ($files, $role) use ($locale, $relation, $relationItem): array {
                                return [
                                    sprintf('blocks[%s-%s][%s][%s]', $relation, $relationItem->id, $role, $locale) => $files,
                                ];
                            }
                        )->toArray();
                    }
                );
            }

            if (isset($relatedItemFormFields['browsers'])) {
                foreach ($relatedItemFormFields['browsers'] as $key => $values) {
                    $repeatersBrowsers[sprintf('blocks[%s-%s][%s]', $relation, $relationItem->id, $key)] = $values;
                }
            }

            $itemFields = method_exists($relationItem, 'toRepeaterArray') ? $relationItem->toRepeaterArray(
            ) : Arr::except($relationItem->attributesToArray(), $translatedFields);

            foreach ($itemFields as $key => $value) {
                $repeatersFields[] = [
                    'name' => sprintf('blocks[%s-%s][%s]', $relation, $relationItem->id, $key),
                    'value' => $value,
                ];
            }

            if (isset($relatedItemFormFields['repeaters'])) {
                foreach ($relatedItemFormFields['repeaters'] as $childRepeaterName => $childRepeaterItems) {
                    $fields['repeaters'][sprintf('blocks-%s-%s_%s', $relation, $relationItem->id, $childRepeaterName)] = $childRepeaterItems;
                    $repeatersFields = array_merge(
                        $repeatersFields,
                        $relatedItemFormFields['repeaterFields'][$childRepeaterName]
                    );
                    $repeatersMedias = array_merge(
                        $repeatersMedias,
                        $relatedItemFormFields['repeaterMedias'][$childRepeaterName]
                    );
                    $repeatersFiles = array_merge(
                        $repeatersFiles,
                        $relatedItemFormFields['repeaterFiles'][$childRepeaterName]
                    );
                    $repeatersBrowsers = array_merge(
                        $repeatersBrowsers,
                        $relatedItemFormFields['repeaterBrowsers'][$childRepeaterName]
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
     * Get all repeaters' model and relation from the $repeaters attribute.
     * The missing information will be inferred by convention of Twill.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getRepeaters()
    {
        return collect($this->repeaters)->map(function ($repeater, $key): array {
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
     * Guess the relation name (shoud be lower camel case, ex. userGroup, contactOffice).
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
