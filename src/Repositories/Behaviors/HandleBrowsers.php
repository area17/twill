<?php

namespace A17\Twill\Repositories\Behaviors;

use A17\Twill\Models\Behaviors\HasMedias;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait HandleBrowsers
{
    /**
     * All browsers used in the model, as an array of browser names:
     * [
     *     'books',
     *     'publications'
     * ]
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
     * @return void
     * @param mixed[] $fields
     */
    public function afterSaveHandleBrowsers(\A17\Twill\Models\Model $object, array $fields)
    {
        foreach ($this->getBrowsers() as $browser) {
            $this->updateBrowser($object, $fields, $browser['relation'], $browser['positionAttribute'], $browser['browserName']);
        }
    }

    /**
     * @return array
     * @param mixed[] $fields
     */
    public function getFormFieldsHandleBrowsers(\A17\Twill\Models\Model $object, array $fields)
    {
        foreach ($this->getBrowsers() as $browser) {
            $relation = $browser['relation'];
            if (collect($object->getRelation())->isNotEmpty()) {
                $fields['browsers'][$browser['browserName']] = $this->getFormFieldsForBrowser($object, $relation, $browser['routePrefix'], $browser['titleKey'], $browser['moduleName']);
            }
        }

        return $fields;
    }

    /**
     * @param string|null $browserName
     * @return void
     * @param mixed[] $fields
     * @param mixed[] $pivotAttributes
     */
    public function updateBrowser(\A17\Twill\Models\Model $object, array $fields, string $relationship, string $positionAttribute = 'position', $browserName = null, array $pivotAttributes = [])
    {
        $browserName = $browserName ?? $relationship;
        $fieldsHasElements = isset($fields['browsers'][$browserName]) && !empty($fields['browsers'][$browserName]);
        $relatedElements = $fieldsHasElements ? $fields['browsers'][$browserName] : [];

        $relatedElementsWithPosition = [];
        $position = 1;

        foreach ($relatedElements as $relatedElement) {
            $relatedElementsWithPosition[$relatedElement['id']] = [$positionAttribute => $position++] + $pivotAttributes;
        }

        if ($object->$relationship() instanceof BelongsTo) {
            $foreignKey = $object->$relationship()->getForeignKeyName();
            $id = Arr::get($relatedElements, '0.id', null);
            $object->update([$foreignKey => $id]);
        } elseif ($object->$relationship() instanceof HasOne ||
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
     * @return void
     * @param mixed[] $fields
     */
    public function updateOrderedBelongsTomany(\A17\Twill\Models\Model $object, array $fields, string $relationship, string $positionAttribute = 'position')
    {
        $this->updateBrowser($object, $fields, $relationship, $positionAttribute);
    }

    /**
     * @return void
     * @param mixed[] $fields
     */
    public function updateRelatedBrowser(\A17\Twill\Models\Model $object, array $fields, string $browserName)
    {
        $object->saveRelated($fields['browsers'][$browserName] ?? [], $browserName);
    }

    /**
     * @param string|null $routePrefix
     * @param string|null $moduleName
     * @return array
     */
    public function getFormFieldsForBrowser(\A17\Twill\Models\Model $object, string $relation, $routePrefix = null, string $titleKey = 'title', $moduleName = null)
    {
        $fields = $this->getRelatedElementsAsCollection($object, $relation);

        if ($fields->isNotEmpty()) {
            return $fields->map(function ($relatedElement) use ($titleKey, $routePrefix, $relation, $moduleName): array {
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

        return [];
    }

    /**
     * @return array
     */
    public function getFormFieldsForRelatedBrowser(\A17\Twill\Models\Model $object, string $relation, $titleKey = 'title')
    {
        return $object->getRelated($relation)->map(function ($relatedElement) use ($titleKey): array {
            return ($relatedElement != null) ? [
                'id' => $relatedElement->id,
                'name' => $relatedElement->titleInBrowser ?? $relatedElement->$titleKey,
                'endpointType' => $relatedElement->getMorphClass(),
            ] + (empty($relatedElement->adminEditUrl) ? [] : [
                'edit' => $relatedElement->adminEditUrl,
            ]) + (classHasTrait($relatedElement, HasMedias::class) ? [
                'thumbnail' => $relatedElement->defaultCmsImage(['w' => 100, 'h' => 100]),
            ] : []) : [];
        })->reject(function ($item): bool {
            return empty($item);
        })->values()->toArray();
    }

    /**
     * Get all browser' detail info from the $browsers attribute.
     * The missing information will be inferred by convention of Twill.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getBrowsers()
    {
        return collect($this->browsers)->map(function ($browser, $key): array {
            $browserName = is_string($browser) ? $browser : $key;
            $moduleName = empty($browser['moduleName']) ? $this->inferModuleNameFromBrowserName($browserName) : $browser['moduleName'];

            return [
                'relation' => empty($browser['relation']) ? $this->inferRelationFromBrowserName($browserName) : $browser['relation'],
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
