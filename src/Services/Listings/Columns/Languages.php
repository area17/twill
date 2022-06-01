<?php

namespace A17\Twill\Services\Listings\Columns;

use A17\Twill\Services\Listings\TableColumn;
use Illuminate\Database\Eloquent\Model;

/**
 * This is an empty one as it is rendered on the vue end.
 * @todo: Refactor to be handled in blade.
 */
class Languages extends TableColumn
{
    public static function make(): static
    {
        $column = new static();
        $column->field('languages');

        return $column;
    }

    protected function getRenderValue(Model $model): string
    {
        return '';
    }
}
