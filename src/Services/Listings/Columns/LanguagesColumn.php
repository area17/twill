<?php

namespace A17\Twill\Services\Listings\Columns;

use A17\Twill\Models\Model;
use A17\Twill\Services\Listings\TableColumn;

/**
 * This is an empty one as it is rendered on the vue end.
 * @todo: Refactor to be handled in blade.
 */
class LanguagesColumn extends TableColumn
{
    public function getRenderValue(Model $model): string
    {
        return '';
    }
}
