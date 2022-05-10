<?php

namespace A17\Twill\Services\Listings;

use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class TableColumns extends Collection
{
    public function getArrayForModelBrowser(BaseModel $model, TableDataContext $tableDataContext): array
    {
        $data = $this->getArrayForModel($model);

        $editUrl = moduleRoute(
            $tableDataContext->moduleName,
            $tableDataContext->routePrefix,
            'edit',
            $model->{$tableDataContext->identifierColumn}
        );

        $data['id'] = $model->{$tableDataContext->identifierColumn};
        $data['name'] = $model->{$tableDataContext->titleColumnKey};
        $data['edit'] = $editUrl;
        $data['endpointType'] = $tableDataContext->endpointType;

        if (!isset($data['thumbnail']) && $tableDataContext->hasMedia) {
            $data['thumbnail'] = $model->defaultCmsImage(['w' => 100, 'h' => 100]);
        }

        return $data;
    }

    public function getArrayForModel(BaseModel $model): array
    {
        $data = [];

        /** @var \A17\Twill\Services\Listings\TableColumn $item */
        foreach ($this->items as $item) {
            $data[$item->getKey()] = $item->renderCell($model);
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
