<?php

namespace A17\Twill\Services\Listings\Columns;

use A17\Twill\Services\Listings\TableColumn;
use Illuminate\Database\Eloquent\Model;

class Boolean extends TableColumn
{
    protected function getRenderValue(Model $model): string
    {
        return $model->{$this->field} ? "✅" : "❌";
    }
}
