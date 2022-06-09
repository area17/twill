<?php

namespace A17\Twill\Services\Listings\Filters;

use Illuminate\Database\Eloquent\Builder;

interface TwillFilterContract
{
    public function apply(\Closure $closure);
    public function applyFilter(Builder $builder): Builder;
}
