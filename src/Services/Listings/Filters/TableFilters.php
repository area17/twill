<?php

namespace A17\Twill\Services\Listings\Filters;

use Illuminate\Support\Collection;

class TableFilters extends Collection
{
    public function toFrontendArray(): array
    {
        $result = [];

        /** @var \A17\Twill\Services\Listings\Filters\TableFilter $filter */
        foreach ($this->items as $filter) {
//
//            if (!$filters->isEnabled()) {
//                continue;
//            }
            $result[$filter->getKey()] = $filter->getOptions();
        }

        return $result;
    }
}
