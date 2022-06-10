<?php

namespace A17\Twill\Services\Listings\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class BasicFilter extends TwillBaseFilter
{
    public const OPTION_ALL = 'all';

    protected function __construct(
        protected ?string $label = null,
        protected ?string $queryString = null,
        protected bool $enabled = true,
        protected ?\Closure $apply = null,
        protected ?Collection $options = null,
        protected mixed $appliedValue = null,
        protected bool $includeAll = false,
        protected mixed $default = null
    ) {
    }

    public function withFilterValue(mixed $value): self
    {
        $this->appliedValue = $value;

        return $this;
    }

    public function includeAll(bool $includeAll = true): self
    {
        $this->includeAll = $includeAll;

        return $this;
    }

    public function options(Collection $options): self
    {
        $this->options = $options;

        return $this;
    }

    public function default(mixed $default): self
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

    public function getOptions(): Collection
    {
        if ($this->includeAll) {
            $this->options->prepend('All', self::OPTION_ALL);
        }
        return $this->options;
    }
}
