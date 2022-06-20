<?php

namespace A17\Twill\Services\Listings\Filters;

use A17\Twill\Repositories\ModuleRepository;
use Illuminate\Support\Collection;

class TableFilters extends Collection
{
    public function toFrontendArray(ModuleRepository $repository): array
    {
        $result = [];

        /** @var \A17\Twill\Services\Listings\Filters\BasicFilter $filter */
        foreach ($this->items as $filter) {
//
//            if (!$filters->isEnabled()) {
//                continue;
//            }
            $result[$filter->getKey()] = $filter->getOptions($repository);
        }

        return $result;
    }
}
