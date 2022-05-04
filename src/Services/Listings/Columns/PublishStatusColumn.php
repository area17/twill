<?php

namespace A17\Twill\Services\Listings\Columns;

use A17\Twill\Models\Model;
use A17\Twill\Services\Listings\TableColumn;

class PublishStatusColumn extends TableColumn
{
    public static function make(string $key): static
    {
        // Publish status column is always in need of this key for vue.
        return new static('published');
    }

    public function getRenderValue(Model $model): string
    {
        return '';
    }
}
