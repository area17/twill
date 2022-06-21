<?php

namespace A17\Twill\Services\Listings\Columns;

use A17\Twill\Models\Contracts\TwillModelContract;
use A17\Twill\Services\Listings\TableColumn;
use Illuminate\Support\Str;

class NestedData extends TableColumn
{
    public static function make(): static
    {
        $item = parent::make();
        $item->field('children');
        return $item;
    }

    protected function getRenderValue(TwillModelContract $model): string
    {
        $nestedCount = $model->{$this->field}()->count();

        return $nestedCount . ' ' . (strtolower(Str::plural($this->title, $nestedCount)));
    }
}
