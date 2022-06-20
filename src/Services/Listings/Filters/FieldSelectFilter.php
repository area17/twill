<?php

namespace A17\Twill\Services\Listings\Filters;

use A17\Twill\Repositories\ModuleRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * This filter shows a select of available options of a certain field.
 *
 * For example, you have a model with a year field, this filter will collect the values and use those to make the filter.
 */
class FieldSelectFilter extends BasicFilter
{
    protected string $field;

    public function __construct()
    {
        $this->includeAll();
    }

    public function applyFilter(Builder $builder): Builder
    {
        if ($this->appliedValue && $this->appliedValue !== self::OPTION_ALL) {
            $builder->where($this->field, $this->appliedValue);
        }

        return $builder;
    }

    public function getOptions(ModuleRepository $repository): Collection
    {
        /** @var Collection $optionsBase */
        $optionsBase = $repository->groupBy($this->field)->pluck($this->field);

        $options = collect(array_combine($optionsBase->all(), $optionsBase->all()));

        if ($this->includeAll) {
            $options->prepend('All', self::OPTION_ALL);
        }

        return $options;
    }

    public function field(string $fieldName): self
    {
        $this->field = $fieldName;

        if ($this->queryString === null) {
            $this->queryString($fieldName);
        }

        return $this;
    }
}
