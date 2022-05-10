<?php

namespace A17\Twill\Services\Listings\Columns;

use A17\Twill\Services\Listings\TableColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class NestedData extends TableColumn
{
    public static function make(): static
    {
        $item = parent::make();
        $item->field('children');
        return $item;
    }

    public function getRenderValue(Model $model): string
    {
        $nestedCount = $model->{$this->field}()->count();

        return $nestedCount . ' ' . (strtolower(Str::plural($this->title, $nestedCount)));
    }
}
