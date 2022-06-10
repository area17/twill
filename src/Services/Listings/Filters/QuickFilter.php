<?php

namespace A17\Twill\Services\Listings\Filters;

use Illuminate\Database\Eloquent\Builder;

class QuickFilter extends TwillBaseFilter
{
    protected function __construct(
        protected ?string $label = null,
        protected ?string $queryString = null,
        protected ?\Closure $amount = null,
        protected bool $enabled = true,
        protected ?\Closure $apply = null
    ) {
    }

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
