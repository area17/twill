<?php

namespace A17\Twill\Services\Listings;

use A17\Twill\Models\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class TableColumns extends Collection
{
    public function getArrayForModel(Model $model): array
    {
        $data = [];

        /** @var \A17\Twill\Services\Listings\TableColumn $item */
        foreach ($this->items as $item) {
            $data[$item->key] = $item->renderCell($model);
        }
        return $data;
    }

    public function toCmsArray(Request $request, bool $sortable = true): array
    {
        $visibleColumns = $request->get('columns') ?? [];

        $tableColumns = [];

        /** @var \A17\Twill\Services\Listings\TableColumn $column */
        foreach ($this->items as $column) {
            $tableColumns[] = $column->toColumnArray($visibleColumns, $sortable);
        }

        return $tableColumns;
    }
}
