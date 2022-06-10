<?php

namespace A17\Twill\Services\Listings\Filters;

use Illuminate\Database\Eloquent\Builder;

class QuickFilter extends TwillBaseFilter
{
    protected ?\Closure $amount = null;

    public function amount(\Closure $callback): self
    {
        $this->amount = $callback;

        return $this;
    }

    public function applyFilter(Builder $builder): Builder
    {
        if ($closure = $this->apply) {
            $closure($builder);
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
