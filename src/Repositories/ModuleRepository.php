<?php

namespace A17\Twill\Repositories;

use A17\Twill\Exceptions\NoCapsuleFoundException;
use A17\Twill\Facades\TwillBlocks;
use A17\Twill\Facades\TwillCapsules;
use A17\Twill\Facades\TwillPermissions;
use A17\Twill\Models\Behaviors\Sortable;
use A17\Twill\Models\Contracts\TwillModelContract;
use A17\Twill\Models\Model;
use A17\Twill\Repositories\Behaviors\HandleBrowsers;
use A17\Twill\Repositories\Behaviors\HandleDates;
use A17\Twill\Repositories\Behaviors\HandleFieldsGroups;
use A17\Twill\Repositories\Behaviors\HandlePermissions;
use A17\Twill\Repositories\Behaviors\HandleRelatedBrowsers;
use A17\Twill\Repositories\Behaviors\HandleRepeaters;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use ReflectionClass;

abstract class ModuleRepository
{
    use HandleDates;
    use HandleBrowsers;
    use HandleRelatedBrowsers;
    use HandleRepeaters;
    use HandleFieldsGroups;
    use HandlePermissions;

    protected TwillModelContract $model;

    /**
     * @var string[]
     */
    protected array $ignoreFieldsBeforeSave = [];

    protected array $countScope = [];

    protected array $fieldsGroups = [];

    public bool $fieldsGroupsFormFieldNamesAutoPrefix = false;

    public string $fieldsGroupsFormFieldNameSeparator = '_';

    public function get(
        array $with = [],
        array $scopes = [],
        array $orders = [],
        int $perPage = 20,
        bool $forcePagination = false,
        array $appliedFilters = []
    ): LengthAwarePaginator|Collection {
        $query = $this->model->with($with);

        $query = $this->filter($query, $scopes);
        $query = $this->order($query, $orders);

        foreach ($appliedFilters as $filter) {
            $query = $filter->applyFilter($query);
        }

        if (! $forcePagination && $this->model instanceof Sortable) {
            return $query->ordered()->get();
        }

        if ($perPage == -1) {
            return $query->get();
        }

        return $query->paginate($perPage);
    }

    public function getCountByStatusSlug(string $slug, array $scope = []): int
    {
        $query = $this->model->where($scope);

        if (
            TwillPermissions::enabled() &&
            (
                TwillPermissions::getPermissionModule(getModuleNameByModel($this->model)) ||
                method_exists($this->model, 'scopeAccessible')
            )
        ) {
            $query = $query->accessible();
        }

        switch ($slug) {
            case 'all':
                return $query->count();
            case 'published':
                return $query->published()->count();
            case 'draft':
                return $query->draft()->count();
            case 'trash':
                return $query->onlyTrashed()->count();
        }

        foreach ($this->traitsMethods(__FUNCTION__) as $method) {
            if (($count = $this->$method($slug, $scope)) !== false) {
                return $count;
            }
        }

        return 0;
    }

    /**
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getById(int $id, array $with = [], array $withCount = []): TwillModelContract
    {
        return once(function () use ($id, $with, $withCount) {
            return $this->model->with($with)->withCount($withCount)->findOrFail($id);
        });
    }

    /**
     * @return Collection<int,TwillModelContract>
     */
    public function listAll(
        string $column = 'title',
        array $orders = [],
        int|string|null $exceptId = null,
        string $pluckBy = 'id'
    ): Collection {
        $query = $this->model::query();

        if ($exceptId) {
            $query = $query->where($this->model->getTable() . '.id', '<>', $exceptId);
        }

        if ($this->model instanceof Sortable) {
            $query = $query->ordered();
        } elseif ($orders !== []) {
            $query = $this->order($query, $orders);
        }

        if ($this->model->isTranslatable()) {
            $query = $query->withTranslation();
        }

        return $query->get()->pluck($column, $pluckBy);
    }

    public function cmsSearch(string $search, array $fields = []): Collection
    {
        $builder = $this->model->latest();

        $translatedAttributes = $this->model->translatedAttributes ?? [];

        foreach ($fields as $field) {
            if (in_array($field, $translatedAttributes, true)) {
                $builder->orWhereTranslationLike($field, "%$search%");
            } else {
                $builder->orWhere($field, getLikeOperator(), "%$search%");
            }
        }

        return $builder->get();
    }

    public function firstOrCreate(array $attributes, array $fields = []): TwillModelContract
    {
        return $this->model->where($attributes)->first() ?? $this->create($attributes + $fields);
    }

    public function create(array $fields): TwillModelContract
    {
        return DB::transaction(function () use ($fields) {
            $original_fields = $fields;

            $fields = $this->prepareFieldsBeforeCreate($fields);

            $model = $this->model->make(Arr::except($fields, $this->getReservedFields()));

            $fields = $this->prepareFieldsBeforeSave($model, $fields);

            $model->fill(Arr::except($fields, $this->getReservedFields()));

            $this->beforeSave($model, $original_fields);

            $model->save();

            $this->afterSaveOriginalData($model, $original_fields);

            $this->afterSave($model, $fields);

            return $model;
        }, 3);
    }

    public function createForPreview(array $fields): TwillModelContract
    {
        $fields = $this->prepareFieldsBeforeCreate($fields);

        $model = $this->model->newInstance(Arr::except($fields, $this->getReservedFields()));

        return $this->hydrate($model, $fields);
    }

    public function updateOrCreate(array $attributes, array $fields): TwillModelContract
    {
        $model = $this->model->where($attributes)->first();

        if (! $model) {
            return $this->create($fields);
        }

        $this->update($model->id, $fields);

        return $model;
    }

    public function update(int|string $id, array $fields): TwillModelContract
    {
        return DB::transaction(function () use ($id, $fields) {
            $model = $this->model->findOrFail($id);

            $original_fields = $fields;

            $this->beforeSave($model, $fields);

            $fields = $this->prepareFieldsBeforeSave($model, $fields);

            $model->fill(Arr::except($fields, $this->getReservedFields()));

            $model->save();

            $this->afterSaveOriginalData($model, $original_fields);

            $this->afterSave($model, $fields);

            return $model->fresh();
        }, 3);
    }

    public function updateBasic(int|string|null|array $id, array $values, array $scopes = []): bool
    {
        return DB::transaction(function () use ($id, $values, $scopes) {
            // apply scopes if no id provided
            if ($id === null) {
                $query = $this->model::query();

                foreach ($scopes as $column => $value) {
                    $query->where($column, $value);
                }

                $query->update($values);

                $query->get()->each(function ($model) use ($values) {
                    /* @var TwillModelContract $model */
                    $this->afterUpdateBasic($model, $values);
                });

                return true;
            }

            // apply to all ids if array of ids provided
            if (is_array($id)) {
                $query = $this->model->whereIn('id', $id);
                $query->update($values);

                $query->get()->each(function ($object) use ($values) {
                    $this->afterUpdateBasic($object, $values);
                });

                return true;
            }

            if ($model = $this->model->find($id)) {
                $model->update($values);
                $this->afterUpdateBasic($model, $values);

                return true;
            }

            return false;
        }, 3);
    }

    public function setNewOrder(array $ids): void
    {
        DB::transaction(function () use ($ids) {
            $this->model::setNewOrder($ids);
        }, 3);
    }

    public function duplicate(int|string $id, string $titleColumnKey = 'title'): ?TwillModelContract
    {
        $newObject = null;

        if ($object = $this->model->find($id)) {
            $newObject = $object->replicate();
            $newObject->save();
            if ($object->isTranslatable()) {
                foreach ($object->translations as $translation) {
                    $relationKey = $newObject->getRelationKey();
                    $newTranslation = $translation->replicate();
                    $newTranslation->{$relationKey} = $newObject->id;
                    $newTranslation->save();
                }
            }

            $this->afterDuplicate($object, $newObject);
        }

        return $newObject;
    }

    public function delete(int|string $id): bool
    {
        return DB::transaction(function () use ($id) {
            if ($object = $this->model->find($id)) {
                if (! method_exists($object, 'canDeleteSafely') || $object->canDeleteSafely()) {
                    $object->delete();
                    $this->afterDelete($object);

                    return true;
                }
            }

            return false;
        }, 3);
    }

    public function bulkDelete(array $ids): bool
    {
        return DB::transaction(function () use ($ids) {
            try {
                Collection::make($ids)->each(function ($id) {
                    $this->delete($id);
                });
            } catch (Exception $exception) {
                Log::error($exception);
                if (config('app.debug')) {
                    throw $exception;
                }

                return false;
            }

            return true;
        }, 3);
    }

    public function forceDelete(int|string $id): bool
    {
        return DB::transaction(function () use ($id) {
            if ($object = $this->model->onlyTrashed()->find($id)) {
                $object->forceDelete();
                $this->afterDelete($object);

                return true;
            }

            return false;
        }, 3);
    }

    public function bulkForceDelete(array $ids): bool
    {
        return DB::transaction(function () use ($ids) {
            try {
                $query = $this->model->onlyTrashed()->whereIn('id', $ids);
                $objects = $query->get();

                $query->forceDelete();

                $objects->each(function ($object) {
                    $this->afterDelete($object);
                });
            } catch (Exception $exception) {
                Log::error($exception);

                return false;
            }

            return true;
        }, 3);
    }

    public function restore(int|string $id): bool
    {
        return DB::transaction(function () use ($id) {
            if ($object = $this->model::withTrashed()->find($id)) {
                $object->restore();
                $this->afterRestore($object);

                return true;
            }

            return false;
        }, 3);
    }

    public function bulkRestore(array $ids): bool
    {
        return DB::transaction(function () use ($ids) {
            try {
                $query = $this->model::withTrashed()->whereIn('id', $ids);
                $objects = $query->get();

                $query->restore();

                $objects->each(function ($object) {
                    $this->afterRestore($object);
                });
            } catch (Exception $exception) {
                Log::error($exception);

                return false;
            }

            return true;
        }, 3);
    }

    public function cleanupFields(?TwillModelContract $object, array $fields): array
    {
        if (property_exists($this->model, 'checkboxes')) {
            foreach ($this->model->checkboxes as $field) {
                if (! $this->shouldIgnoreFieldBeforeSave($field)) {
                    $fields[$field] = isset($fields[$field]) && ! empty($fields[$field]);
                }
            }
        }

        if (property_exists($this->model, 'nullable')) {
            foreach ($this->model->nullable as $field) {
                if (! isset($fields[$field]) && ! $this->shouldIgnoreFieldBeforeSave($field)) {
                    $fields[$field] = null;
                }
            }
        }

        foreach ($fields as $key => $value) {
            if (! $this->shouldIgnoreFieldBeforeSave($key)) {
                if ($value === []) {
                    $fields[$key] = null;
                }

                if ($value === '') {
                    $fields[$key] = null;
                }
            }
        }

        return $fields;
    }

    /**
     * @return array|<missing>
     */
    public function prepareFieldsBeforeCreate(array $fields): array
    {
        $fields = $this->cleanupFields(null, $fields);

        foreach ($this->traitsMethods(__FUNCTION__) as $method) {
            $fields = $this->$method($fields);
        }

        return $fields;
    }

    /**
     * @return array|<missing>
     */
    public function prepareFieldsBeforeSave(TwillModelContract $object, array $fields): array
    {
        $fields = $this->cleanupFields($object, $fields);

        foreach ($this->traitsMethods(__FUNCTION__) as $method) {
            $fields = $this->$method($object, $fields);
        }

        return $fields;
    }

    public function afterUpdateBasic(TwillModelContract $object, array $fields): void
    {
        foreach ($this->traitsMethods(__FUNCTION__) as $method) {
            $this->$method($object, $fields);
        }
    }

    public function beforeSave(TwillModelContract $object, array $fields): void
    {
        foreach ($this->traitsMethods(__FUNCTION__) as $method) {
            $this->$method($object, $fields);
        }
    }

    public function afterSaveOriginalData(TwillModelContract $model, array $fields): void
    {
        foreach ($this->traitsMethods(__FUNCTION__) as $method) {
            $this->$method($model, $fields);
        }
    }

    public function afterSave(TwillModelContract $model, array $fields): void
    {
        foreach ($this->traitsMethods(__FUNCTION__) as $method) {
            $this->$method($model, $fields);
        }
    }

    public function afterDelete(TwillModelContract $object): void
    {
        foreach ($this->traitsMethods(__FUNCTION__) as $method) {
            $this->$method($object);
        }
    }

    public function afterDuplicate(TwillModelContract $old, TwillModelContract $new): void
    {
        foreach ($this->traitsMethods(__FUNCTION__) as $method) {
            $this->$method($old, $new);
        }
    }

    public function afterRestore(TwillModelContract $object): void
    {
        foreach ($this->traitsMethods(__FUNCTION__) as $method) {
            $this->$method($object);
        }
    }

    public function hydrate(TwillModelContract $model, array $fields): TwillModelContract
    {
        foreach ($this->traitsMethods(__FUNCTION__) as $method) {
            $model = $this->$method($model, $fields);
        }

        return $model;
    }

    public function getFormFields(TwillModelContract $object): array
    {
        $fields = $object->attributesToArray();

        foreach ($this->traitsMethods(__FUNCTION__) as $method) {
            $fields = $this->$method($object, $fields);
        }

        return $fields;
    }

    public function filter(Builder $query, array $scopes = []): Builder
    {
        $likeOperator = getLikeOperator();

        foreach ($this->traitsMethods(__FUNCTION__) as $method) {
            $this->$method($query, $scopes);
        }

        unset($scopes['search']);

        if (isset($scopes['exceptIds'])) {
            $query->whereNotIn($this->model->getTable() . '.id', $scopes['exceptIds']);
            unset($scopes['exceptIds']);
        }

        foreach ($scopes as $column => $value) {
            if (method_exists($this->model, 'scope' . ucfirst($column))) {
                $query->$column();
            } elseif (is_array($value)) {
                $query->whereIn($column, $value);
            } elseif ($column[0] === '%') {
                $value && ($value[0] === '!') ? $query->where(
                    substr($column, 1),
                    "not $likeOperator",
                    '%' . substr($value, 1) . '%'
                ) : $query->where(substr($column, 1), $likeOperator, '%' . $value . '%');
            } elseif (isset($value[0]) && $value[0] === '!') {
                $query->where($column, '<>', substr($value, 1));
            } elseif ($value !== '') {
                $query->where($column, $value);
            }
        }

        return $query;
    }

    public function order(Builder $builder, array $orders = []): Builder
    {
        foreach ($this->traitsMethods(__FUNCTION__) as $method) {
            $this->$method($builder, $orders);
        }

        foreach ($orders as $column => $direction) {
            if (is_array($direction)) {
                $callback = $direction['callback'];
                $builder = $callback($builder, $direction['direction']);
            } else {
                $builder->orderBy($column, $direction);
            }
        }

        return $builder;
    }

    public function updateOneToMany(
        TwillModelContract $object,
        array $fields,
        string $relationship,
        string $formField,
        string $attribute
    ): void {
        if (isset($fields[$formField])) {
            foreach ($fields[$formField] as $id) {
                $object->$relationship()->updateOrCreate([$attribute => $id]);
            }

            foreach ($object->$relationship as $relationshipObject) {
                if (! in_array($relationshipObject->$attribute, $fields[$formField])) {
                    $relationshipObject->delete();
                }
            }
        } else {
            $object->$relationship()->delete();
        }
    }

    public function updateMultiSelect(TwillModelContract $object, array $fields, string $relationship): void
    {
        $object->$relationship()->sync($fields[$relationship] ?? []);
    }

    public function addRelationFilterScope(
        Builder $query,
        array &$scopes,
        string $scopeField,
        string $scopeRelation
    ): void {
        if (isset($scopes[$scopeField])) {
            $id = $scopes[$scopeField];
            $query->whereHas($scopeRelation, function ($query) use ($id, $scopeField) {
                $query->where($scopeField, $id);
            });
            unset($scopes[$scopeField]);
        }
    }

    public function addLikeFilterScope(Builder $query, array &$scopes, string $scopeField): void
    {
        if (isset($scopes[$scopeField]) && is_string($scopes[$scopeField])) {
            $query->where($scopeField, getLikeOperator(), '%' . $scopes[$scopeField] . '%');
            unset($scopes[$scopeField]);
        }
    }

    public function isUniqueFeature(): bool
    {
        return false;
    }

    public function addIgnoreFieldsBeforeSave(array $ignore = []): void
    {
        $this->ignoreFieldsBeforeSave = is_array($ignore)
            ? array_merge($this->ignoreFieldsBeforeSave, $ignore)
            : array_merge($this->ignoreFieldsBeforeSave, [$ignore]);
    }

    public function shouldIgnoreFieldBeforeSave(string $ignore): bool
    {
        return in_array($ignore, $this->ignoreFieldsBeforeSave, true);
    }

    /**
     * @return string[]
     */
    public function getReservedFields(): array
    {
        return [
            'medias',
            'browsers',
            'repeaters',
            'blocks',
        ];
    }

    protected function getModelRepository(
        string $relation,
        string|ModuleRepository|null $modelOrRepository = null
    ): ModuleRepository {
        if (! $modelOrRepository) {
            if (class_exists($relation) && (new $relation()) instanceof Model) {
                $modelOrRepository = Str::afterLast($relation, '\\');
            } else {
                $morphedModel = Relation::getMorphedModel($relation);
                if (class_exists($morphedModel) && (new $morphedModel()) instanceof Model) {
                    $modelOrRepository = (new ReflectionClass($morphedModel))->getShortName();
                } else {
                    $modelOrRepository = ucfirst(Str::singular($relation));
                }
            }
        }

        $repository = class_exists($modelOrRepository)
            ? App::make($modelOrRepository)
            : $modelOrRepository;

        if ($repository instanceof self) {
            return $repository;
        }

        $class = Config::get('twill.namespace') . '\\Repositories\\' . ucfirst($modelOrRepository) . 'Repository';

        if (class_exists($class)) {
            return App::make($class);
        }

        try {
            $capsule = TwillCapsules::getCapsuleForModel($modelOrRepository);

            return App::make($capsule->getRepositoryClass());
        } catch (NoCapsuleFoundException) {
            throw new \Exception("Repository class not found for model '$modelOrRepository'");
        }
    }

    protected function traitsMethods(?string $method = null): array
    {
        $method = $method ?? debug_backtrace()[1]['function'];

        $traits = array_values(class_uses_recursive(static::class));

        $uniqueTraits = array_unique(array_map('class_basename', $traits));

        $methods = array_map(function (string $trait) use ($method) {
            return $method . $trait;
        }, $uniqueTraits);

        return array_filter($methods, function (string $method) {
            return method_exists(static::class, $method);
        });
    }

    /**
     * @deprecated use the helper getLikeOperator directly.
     */
    protected function getLikeOperator(): string
    {
        return getLikeOperator();
    }

    public function __call(string $method, array $parameters): mixed
    {
        return $this->model->$method(...$parameters);
    }

    public function hasBehavior(string $behavior): bool
    {
        $hasBehavior = classHasTrait($this, 'A17\Twill\Repositories\Behaviors\Handle' . ucfirst($behavior));

        if (Str::startsWith($behavior, 'translation')) {
            return $hasBehavior && $this->model->isTranslatable();
        }

        return $hasBehavior;
    }

    public function isTranslatable($column): bool
    {
        return $this->model->isTranslatable($column);
    }

    public function getBaseModel(): TwillModelContract
    {
        return $this->model;
    }
}
