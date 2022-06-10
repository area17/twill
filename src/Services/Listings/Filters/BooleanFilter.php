<?php

namespace A17\Twill\Services\Listings\Filters;

use Illuminate\Database\Eloquent\Builder;

class BooleanFilter extends BasicFilter
{
    protected string $field;

    public function __construct()
    {
        // @PRtodo: introduce translatable string
        $this->options(collect(['true' => twillTrans('Yes'), 'false' => twillTrans('No')]));
        $this->includeAll();
    }

    public function applyFilter(Builder $builder): Builder
    {
        if (($this->appliedValue === 'true')) {
            return $builder->where($this->field, '=', true);
        }

        if (($this->appliedValue === 'false')) {
            return $builder->where($this->field, '=', false);
        }

        return $builder;
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
