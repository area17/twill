<?php

namespace A17\Twill\Services\Listings\Columns;

use A17\Twill\Models\Contracts\TwillModelContract;
use A17\Twill\Services\Listings\TableColumn;

class PublishStatus extends TableColumn
{
    public static function make(): static
    {
        // Publish status column is always in need of this key for vue.
        $column = new static();
        $column->field('published');

        return $column;
    }

    protected function getRenderValue(TwillModelContract $model): string
    {
        return '';
    }
}
