<?php

namespace A17\Twill\Services\Listings\Columns;

use A17\Twill\Models\Model;
use A17\Twill\Services\Listings\TableColumn;

class Boolean extends TableColumn
{
    protected function getRenderValue(Model $model): string
    {
        return $model->{$this->field} ? "✅" : "❌";
    }
}
