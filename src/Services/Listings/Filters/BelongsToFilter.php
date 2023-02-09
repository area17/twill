<?php

namespace A17\Twill\Services\Listings\Filters;

use A17\Twill\Repositories\ModuleRepository;
use A17\Twill\Services\Listings\Filters\Exceptions\MissingModelForFilterException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * This filter uses a relation (BelongsTo) to make a filter select.
 */
class BelongsToFilter extends BasicFilter
{
    protected string $field;
    /** @var \Illuminate\Database\Eloquent\Model $model */
    protected ?string $model = null;
    private string $valueLabelField = 'title';

    public function applyFilter(Builder $builder): Builder
    {
        if ($this->appliedValue && $this->appliedValue !== self::OPTION_ALL) {
            /** @var \Illuminate\Database\Eloquent\Model $modelClass */
            $modelClass = $this->getModel();

            $builder->whereHas($this->field, function (Builder $builder) use ($modelClass) {
                $builder->where((new $modelClass())->getKeyName(), $this->appliedValue);
            });
        }

        return $builder;
    }

    public function getOptions(ModuleRepository $repository): Collection
    {
        /** @var \A17\Twill\Models\Model $model */
        $model = $this->getModel();

        $query = $model::query();

        if ((new $model())->isTranslatable()) {
            $query = $query->withTranslation();
        }

        $options = $query->get()->pluck($this->valueLabelField, $this->model::make()->getKeyName());

        if ($this->includeAll) {
            $options->prepend('All', self::OPTION_ALL);
        }

        return $options;
    }

    /**
     * The relation field to use, this is usually something like "partner".
     */
    public function field(string $fieldName): static
    {
        $this->field = $fieldName;

        if ($this->model === null) {
            try {
                $this->model(getModelByModuleName($fieldName));
            } catch (\Exception $e) {
            }
        }

        if ($this->queryString === null) {
            $this->queryString($fieldName);
        }

        return $this;
    }

    /**
     * The model of the relation target, if field is `partner` this would be `Partner::class`.
     */
    public function model(string $model): static
    {
        $this->model = $model;

        return $this;
    }

    public function getModel(): string
    {
        if (!$this->model) {
            throw new MissingModelForFilterException('Model is not set for BelongsToFilter ' . $this->field);
        }

        return $this->model;
    }

    /**
     * The field name that we use for displaying the item label.
     */
    public function valueLabelField(string $valueLabelField): static
    {
        $this->valueLabelField = $valueLabelField;

        return $this;
    }
}
