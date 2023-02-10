<?php

namespace A17\Twill\Services\Listings\Columns;

use A17\Twill\Models\Contracts\TwillModelContract;
use A17\Twill\Services\Listings\TableColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class NestedData extends TableColumn
{
    public static function make(): static
    {
        $item = parent::make();
        $item->field('children');
        return $item;
    }

    public function sortable(bool $sortable = true): static
    {
        if ($sortable && $this->sortFunction === null) {
            $this->order(function (Builder $builder, string $direction) {
                return $builder->withCount($this->field)->orderBy($this->field . '_count', $direction);
            });
        }
        return parent::sortable($sortable);
    }

    protected function getRenderValue(TwillModelContract $model): string
    {
        $nestedCount = $model->{$this->field}()->count();

        return $nestedCount . ' ' . (strtolower(Str::plural($this->title, $nestedCount)));
    }
}
