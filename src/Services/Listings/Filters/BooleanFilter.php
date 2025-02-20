<?php

namespace A17\Twill\Services\Listings\Filters;

use Illuminate\Database\Eloquent\Builder;

class BooleanFilter extends BasicFilter
{
    protected string $field;

    public const TRUE = 'yes';
    public const FALSE = 'no';

    public static function make(): static
    {
        $filter = parent::make();
        $filter->options(
            collect([
                self::TRUE => twillTrans('twill::lang.listing.filter.yes'),
                self::FALSE => twillTrans('twill::lang.listing.filter.no'),
            ])
        );
        return $filter;
    }

    public function applyFilter(Builder $builder): Builder
    {
        if (($this->appliedValue === self::TRUE)) {
            return $builder->where($this->field, '=', true);
        }

        if (($this->appliedValue === self::FALSE)) {
            return $builder->where($this->field, '=', false);
        }

        return $builder;
    }

    public function field(string $fieldName): static
    {
        $this->field = $fieldName;

        if ($this->queryString === null) {
            $this->queryString($fieldName);
        }

        return $this;
    }
}
