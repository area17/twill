<?php

namespace A17\Twill\Services\Listings\Filters;

use A17\Twill\Repositories\ModuleRepository;
use A17\Twill\Services\Listings\Filters\Exceptions\FilterOptionsMissingException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class BasicFilter extends TwillBaseFilter
{
    public const OPTION_ALL = 'all';

    protected ?Collection $options = null;
    protected mixed $appliedValue = null;
    protected bool $includeAll = true;
    protected mixed $default = null;

    /**
     * Sets the applied value of the filter.
     *
     * This is usually something you do not want to run manually.
     */
    public function withFilterValue(mixed $value): static
    {
        $this->appliedValue = $value;

        return $this;
    }

    /**
     * This removes the "All" option.
     */
    public function withoutIncludeAll(bool $removeIncludeAll = true): static
    {
        $this->includeAll = !$removeIncludeAll;

        return $this;
    }

    /**
     * Sets the options that can be used to select, it should be a key->value collection.
     */
    public function options(Collection $options): static
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Set the default value of the filter.
     */
    public function default(mixed $default): static
    {
        $this->default = $default;

        return $this;
    }

    public function getDefaultValue(): mixed
    {
        if ($this->default) {
            return $this->default;
        }

        if ($this->includeAll) {
            return self::OPTION_ALL;
        }

        return null;
    }

    public function applyFilter(Builder $builder): Builder
    {
        if (($this->appliedValue !== self::OPTION_ALL) && $closure = $this->apply) {
            $closure($builder, $this->appliedValue);
        }

        return $builder;
    }

    public function getKey(): string
    {
        return $this->queryString . 'List';
    }

    public function getOptions(ModuleRepository $repository): Collection
    {
        if ($this->options === null) {
            throw new FilterOptionsMissingException($this->queryString . ' filter is missing options');
        }
        if ($this->includeAll) {
            $this->options->prepend('All', self::OPTION_ALL);
        }
        return $this->options;
    }
}
