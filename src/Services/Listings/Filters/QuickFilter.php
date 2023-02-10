<?php

namespace A17\Twill\Services\Listings\Filters;

use Illuminate\Database\Eloquent\Builder;

class QuickFilter extends TwillBaseFilter
{
    protected ?\Closure $amount = null;
    protected ?string $scope = null;
    protected bool $isDefaultQuickFilter = false;

    /**
     * The callback that will tell the filter how many results there are.
     */
    public function amount(\Closure $callback): static
    {
        $this->amount = $callback;

        return $this;
    }

    public function isDefault(): bool
    {
        return $this->isDefaultQuickFilter;
    }

    public function default(): static
    {
        $this->isDefaultQuickFilter = true;

        return $this;
    }

    /**
     * The scope to apply.
     */
    public function scope(string $scope): static
    {
        $this->scope = $scope;

        return $this;
    }

    public function applyFilter(Builder $builder): Builder
    {
        if ($closure = $this->apply) {
            $closure($builder);
        } elseif ($this->scope) {
            $builder->scopes($this->scope);
        }

        return $builder;
    }

    public function toArray(): array
    {
        $callback = $this->amount;
        return [
            'name' => $this->getLabel(),
            'slug' => $this->getQueryString(),
            'number' => $callback ? $callback() : null,
        ];
    }
}
