<?php

namespace A17\Twill\Repositories\Behaviors;

use A17\Twill\Models\Behaviors\HasMedias;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait HandleBrowsers
{
    /**
     * All browsers used in the model, as an array of browser names:
     * [
     *     'books',
     *     'publications'
     * ].
     *
     * When only the browser name is given, the rest of the parameters are inferred from the name.
     * The parameters can also be overridden with an array:
     * [
     *     'books',
     *     'publication' => [
     *         'routePrefix' => 'collections',
     *         'titleKey' => 'name'
     *     ]
     * ]
     *
     * @var array
     */
    protected $browsers = [];

    /**
     * @param \A17\Twill\Models\Model $object
     * @param array $fields
     * @return void
     */
    public function afterSaveHandleBrowsers($object, $fields)
    {
        foreach ($this->getBrowsers() as $browser) {
            $this->updateBrowser(
                $object,
                $fields,
                $browser['relation'],
                $browser['positionAttribute'],
                $browser['browserName']
            );
        }
    }

    /**
     * @param \A17\Twill\Models\Model $object
     * @param array $fields
     * @return array
     */
    public function getFormFieldsHandleBrowsers($object, $fields)
    {
        foreach ($this->getBrowsers() as $browser) {
            $relation = $browser['relation'];
            if (collect($object->$relation)->isNotEmpty()) {
                $fields['browsers'][$browser['browserName']] = $this->getFormFieldsForBrowser(
                    $object,
                    $relation,
                    $browser['routePrefix'],
                    $browser['titleKey'],
                    $browser['moduleName']
                );
            }
        }

        return $fields;
    }

    /**
     * @param \A17\Twill\Models\Contracts\TwillModelContract $object
     * @param array $fields
     * @param string $relationship
     * @param string $positionAttribute
     * @param string|null $browserName
     * @param array $pivotAttributes
     * @return void
     */
    public function updateBrowser(
        $object,
        $fields,
        $relationship,
        $positionAttribute = 'position',
        $browserName = null,
        $pivotAttributes = []
    ) {
        $browserName = $browserName ?? $relationship;
        $fieldsHasElements = isset($fields['browsers'][$browserName]) && !empty($fields['browsers'][$browserName]);
        $relatedElements = $fieldsHasElements ? $fields['browsers'][$browserName] : [];

        $relatedElementsWithPosition = [];
        $position = 1;

        foreach ($relatedElements as $relatedElement) {
            $relatedElementsWithPosition[$relatedElement['id']] = [$positionAttribute => $position++] + $pivotAttributes;
        }

        if ($object->$relationship() instanceof BelongsTo) {
            $isMorphTo = method_exists($object, $relationship) && $object->$relationship() instanceof MorphTo;

            $foreignKey = $object->$relationship()->getForeignKeyName();
            $id = Arr::get($relatedElements, '0.id');

            // Set the target id.
            $object->$foreignKey = $id;

            // If it is a morphTo, we also update the type.
            if ($isMorphTo) {
                $type = Arr::get($relatedElements, '0.endpointType');
                $object->{$object->$relationship()->getMorphType()} = $type;
            }

            $object->save();
        } elseif (
            $object->$relationship() instanceof HasOne ||
            $object->$relationship() instanceof HasMany
        ) {
            $this->updateBelongsToInverseBrowser($object, $relationship, $relatedElements);
        } else {
            $object->$relationship()->sync($relatedElementsWithPosition);
        }
    }

    private function updateBelongsToInverseBrowser($object, $relationship, $updatedElements)
    {
        $foreignKey = $object->$relationship()->getForeignKeyName();
        $relatedModel = $object->$relationship()->getRelated();
        $related = $this->getRelatedElementsAsCollection($object, $relationship);

        $relatedModel
            ->whereIn('id', $related->pluck('id'))
            ->update([$foreignKey => null]);

        $updated = $relatedModel
            ->whereIn('id', collect($updatedElements)->pluck('id'))
            ->get();

        if ($updated->isNotEmpty()) {
            $object->$relationship()->saveMany($updated);
        }
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
     * @param \A17\Twill\Models\Model $object
     * @param array $fields
     * @param string $browserName
     * @return void
     */
    public function updateRelatedBrowser($object, $fields, $browserName)
    {
        $object->saveRelated($fields['browsers'][$browserName] ?? [], $browserName);
    }

    /**
     * @param \A17\Twill\Models\Contracts\TwillModelContract $object
     * @param string $relation
     * @param string|null $routePrefix
     * @param string $titleKey
     * @param string|null $moduleName
     * @return array
     */
    public function getFormFieldsForBrowser(
        $object,
        $relation,
        $routePrefix = null,
        $titleKey = 'title',
        $moduleName = null
    ) {
        $fields = $this->getRelatedElementsAsCollection($object, $relation);

        $isMorphTo = method_exists($object, $relation) && $object->$relation() instanceof MorphTo;

        if ($fields->isNotEmpty()) {
            return $fields->map(
                function ($relatedElement) use ($titleKey, $routePrefix, $relation, $moduleName, $isMorphTo) {
                    if ($isMorphTo && !$moduleName) {
                        // @todo: Maybe there is an existing helper for this?
                        $moduleName = Str::plural(Arr::last(explode('\\', get_class($relatedElement))));
                    }

                    return [
                            'id' => $relatedElement->id,
                            'name' => $relatedElement->titleInBrowser ?? $relatedElement->$titleKey,
                            'edit' => $relatedElement->adminEditUrl ?? moduleRoute(
                                $moduleName ?? $relation,
                                $routePrefix ?? '',
                                'edit',
                                $relatedElement->id
                            ),
                            'endpointType' => $relatedElement->getMorphClass(),
                        ] + (classHasTrait($relatedElement, HasMedias::class) ? [
                            'thumbnail' => $relatedElement->defaultCmsImage(['w' => 100, 'h' => 100, 'fit' => 'crop']),
                        ] : []);
                }
            )->toArray();
        }

        return [];
    }

    /**
     * @param \A17\Twill\Models\Model $object
     * @param string $relation
     * @return array
     */
    public function getFormFieldsForRelatedBrowser($object, $relation, $titleKey = 'title')
    {
        return $object->getRelated($relation)->map(function ($relatedElement) use ($titleKey) {
            return ($relatedElement != null) ? [
                    'id' => $relatedElement->id,
                    'name' => $relatedElement->titleInBrowser ?? $relatedElement->$titleKey,
                    'endpointType' => $relatedElement->getMorphClass(),
                    'edit' => $this->getAdminEditUrl($relatedElement),
                ] + (classHasTrait($relatedElement, HasMedias::class) ? [
                    'thumbnail' => $relatedElement->defaultCmsImage(['w' => 100, 'h' => 100, 'fit' => 'crop']),
                ] : []) : [];
        })->reject(function ($item) {
            return empty($item);
        })->values()->toArray();
    }

    /**
     * @param $object
     * @return mixed|string
     */
    private function getAdminEditUrl($object): mixed
    {
        if (!empty($object->adminEditUrl)) {
            return $object->adminEditUrl;
        }

        $relatedType = $object->getRelation('pivot')->related_type;
        $relation = strpos($relatedType, '\\')
            ? getModuleNameByModel($relatedType)
            : $relatedType;

        return moduleRoute(
            $relation,
            config('twill.block_editor.browser_route_prefixes.' . $relation),
            'edit',
            $object->id
        );
    }

    /**
     * Get all browser' detail info from the $browsers attribute.
     * The missing information will be inferred by convention of Twill.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getBrowsers()
    {
        return collect($this->browsers)->map(function ($browser, $key) {
            $browserName = is_string($browser) ? $browser : $key;
            $moduleName = empty($browser['moduleName']) ? $this->inferModuleNameFromBrowserName(
                $browserName
            ) : $browser['moduleName'];

            return [
                'relation' => empty($browser['relation']) ? $this->inferRelationFromBrowserName(
                    $browserName
                ) : $browser['relation'],
                'routePrefix' => isset($browser['routePrefix']) ? $browser['routePrefix'] : null,
                'titleKey' => empty($browser['titleKey']) ? 'title' : $browser['titleKey'],
                'moduleName' => $moduleName,
                'model' => empty($browser['model']) ? $this->inferModelFromModuleName($moduleName) : $browser['model'],
                'positionAttribute' => empty($browser['positionAttribute']) ? 'position' : $browser['positionAttribute'],
                'browserName' => $browserName,
            ];
        })->values();
    }

    /**
     * Guess the browser's relation name (shoud be lower camel case, ex. userGroup, contactOffice).
     */
    protected function inferRelationFromBrowserName(string $browserName): string
    {
        return Str::camel($browserName);
    }

    /**
     * Guess the module's model name (should be singular upper camel case, ex. User, ArticleType).
     */
    protected function inferModelFromModuleName(string $moduleName): string
    {
        return Str::studly(Str::singular($moduleName));
    }

    /**
     * Guess the browser's module name (should be plural lower camel case, ex. userGroups, contactOffices).
     */
    protected function inferModuleNameFromBrowserName(string $browserName): string
    {
        return Str::camel(Str::plural($browserName));
    }

    private function getRelatedElementsAsCollection($object, $relation)
    {
        return collect(
            $object->$relation instanceof EloquentModel ? [$object->$relation] : $object->$relation
        );
    }
}
