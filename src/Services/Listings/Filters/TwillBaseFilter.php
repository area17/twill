<?php

namespace A17\Twill\Services\Listings\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

abstract class TwillBaseFilter
{
    protected ?string $label = null;
    protected ?string $queryString = null;
    protected bool $enabled = true;
    protected ?\Closure $apply = null;

    abstract public function applyFilter(Builder $builder): Builder;

    final public function __construct()
    {
    }

    public static function make(): static
    {
        return new static();
    }

    /**
     * Set a label to use for the filter.
     */
    public function label(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    /**
     * Set the query string to use in the url
     */
    public function queryString(string $queryString): static
    {
        $this->queryString = $queryString;

        // If there is no label, we set it automatically.
        if ($this->label === null) {
            $this->label(Str::lower(Str::plural(Str::replace('_', ' ', $queryString))));
        }

        return $this;
    }

    public function getQueryString(): string
    {
        return $this->queryString;
    }

    /**
     * When passing a boolean, the filter will only be enabled when it is true.
     */
    public function onlyEnableWhen(bool $enable = true): static
    {
        $this->enabled = $enable;

        return $this;
    }

    /**
     * When passing a boolean, the filter will be disabled when it is true.
     */
    public function disable(bool $disable = true): static
    {
        $this->enabled = !$disable;

        return $this;
    }

    /**
     * The closure to apply the filter.
     */
    public function apply(\Closure $closure): static
    {
        $this->apply = $closure;

        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }
}
