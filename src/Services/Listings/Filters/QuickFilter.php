<?php

namespace A17\Twill\Services\Listings\Filters;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;

class QuickFilter implements Arrayable, TwillFilterContract
{
    protected function __construct(
        protected ?string $label = null,
        protected ?string $queryString = null,
        protected ?\Closure $amount = null,
        protected bool $enabled = true,
        protected ?\Closure $apply = null
    ) {
    }

    public static function make(): self
    {
        return new self();
    }

    public function label(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function queryString(string $queryString): self
    {
        $this->queryString = $queryString;

        return $this;
    }

    public function amountClosure(\Closure $callback): self
    {
        $this->amount = $callback;

        return $this;
    }

    public function onlyEnableWhen(bool $enable = true): self
    {
        $this->enabled = $enable;

        return $this;
    }

    public function disable(bool $disable = true): self
    {
        $this->enabled = !$disable;

        return $this;
    }

    public function apply(\Closure $closure)
    {
        $this->apply = $closure;

        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function getQueryString(): string
    {
        return $this->queryString;
    }

    public function applyFilter(Builder $builder, mixed $value = null): Builder
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
            'name' => $this->label,
            'slug' => $this->queryString,
            'number' => $callback ? $callback() : null,
        ];
    }
}
