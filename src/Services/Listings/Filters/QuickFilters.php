<?php

namespace A17\Twill\Services\Listings\Filters;

use Illuminate\Support\Collection;

/**
 * @todo: We have to implemenet something that either enforces a "all" filter,
 * or we add the option to define ->default() on a quickFilter.
 * We could also apply default to the first filter if it was not specified.
 */
class QuickFilters extends Collection
{
    public function toFrontendArray(): array
    {
        $result = [];

        foreach ($this->items as $statusFilter) {
            if (!$statusFilter->isEnabled()) {
                continue;
            }
            $result[] = $statusFilter->toArray();
        }

        return $result;
    }
}
