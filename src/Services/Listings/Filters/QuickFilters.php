<?php

namespace A17\Twill\Services\Listings\Filters;

use Illuminate\Support\Collection;

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
