<?php

namespace A17\Twill\Services\Listings\Columns;

use A17\Twill\Models\Contracts\TwillModelContract;
use A17\Twill\Services\Listings\TableColumn;

class Boolean extends TableColumn
{
    protected function getRenderValue(TwillModelContract $model): string
    {
        return $model->{$this->field} ? "✅" : "❌";
    }
}
