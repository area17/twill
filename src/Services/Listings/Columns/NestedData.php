<?php

namespace A17\Twill\Services\Listings\Columns;

use A17\Twill\Models\Model;
use A17\Twill\Services\Listings\TableColumn;
use Illuminate\Support\Str;

class NestedData extends TableColumn
{
    public function getRenderValue(Model $model): string
    {
        $nestedCount = $model->{$this->field}->count();

        return $nestedCount . ' ' . (strtolower(Str::plural($this->title, $nestedCount)));
    }
}
