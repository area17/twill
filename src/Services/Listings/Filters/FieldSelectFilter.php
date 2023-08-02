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
    /**
     * The option not set allows to filter by "null" values.
     * @var string
     */
    public const OPTION_NOT_SET = 'null';

    protected string $field;
    private bool $addWithoutValueOption = false;

    public function applyFilter(Builder $builder): Builder
    {
        if ($this->appliedValue && $this->appliedValue !== self::OPTION_ALL) {
            if ($this->appliedValue === self::OPTION_NOT_SET) {
                $builder->whereNull($this->field);
            } else {
                $builder->where($this->field, $this->appliedValue);
            }
        }

        return $builder;
    }

    public function getOptions(ModuleRepository $repository): Collection
    {
        /** @var Collection $optionsBase */
        $optionsBase = $repository->groupBy($this->field)->pluck($this->field);

        $options = collect(array_combine($optionsBase->all(), $optionsBase->all()));

        if ($options->has(null)) {
            $options = $options->forget([null]);

            if ($this->addWithoutValueOption) {
                $options->put(self::OPTION_NOT_SET, twillTrans('twill::lang.listing.filter.not-set'));
            }
        }

        if ($this->includeAll) {
            $options->prepend('All', self::OPTION_ALL);
        }

        return $options;
    }

    public function field(string $fieldName): static
    {
        $this->field = $fieldName;

        if ($this->queryString === null) {
            $this->queryString($fieldName);
        }

        return $this;
    }

    /**
     * This adds the "Without value" option if there are result with "null" value.
     */
    public function withWithoutValueOption(bool $withoutValueOption = true): static
    {
        $this->addWithoutValueOption = $withoutValueOption;

        return $this;
    }
}
