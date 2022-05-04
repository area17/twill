<?php

namespace A17\Twill\Services\Listings\Columns;

use A17\Twill\Models\Model;
use A17\Twill\Services\Listings\TableColumn;

/**
 * This is an empty one as it is rendered on the vue end.
 * @todo: Refactor to be handled in blade.
 */
class Languages extends TableColumn
{
    public static function make(): static
    {
        // Publish status column is always in need of this key for vue.
        $column = new static();
        $column->field('languages');

        return $column;
    }

    public function getRenderValue(Model $model): string
    {
        return '';
    }
}
