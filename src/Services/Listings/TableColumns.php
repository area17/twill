<?php

namespace A17\Twill\Services\Listings;

use A17\Twill\Models\Contracts\TwillModelContract;
use Illuminate\Http\Request;
use Illuminate\Routing\Exceptions\UrlGenerationException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class TableColumns extends Collection
{
    public function getArrayForModelBrowser(TwillModelContract $model, TableDataContext $tableDataContext): array
    {
        $data = $this->getArrayForModel($model);

        try {
            $editUrl = moduleRoute(
                $tableDataContext->moduleName,
                $tableDataContext->routePrefix,
                'edit',
                [$model->{$tableDataContext->identifierColumn}]
            );
        } catch (UrlGenerationException $e) {
            if (app()->environment() === 'local' || config('app.debug')) {
                report($e);
                Log::notice(
                    "Twill warning: The url for the \"{$tableDataContext->moduleName}\" browser items can't be resolved."
                );
            }
        }

        $data['id'] = $model->{$tableDataContext->identifierColumn};
        $data['name'] = $model->{$tableDataContext->titleColumnKey};
        $data['edit'] = $editUrl ?? null;
        $data['endpointType'] = $tableDataContext->endpointType;
        $data['repeaterFields'] = $tableDataContext->repeaterFields;

        if (!isset($data['thumbnail']) && $tableDataContext->hasMedia) {
            $data['thumbnail'] = $model->defaultCmsImage(['w' => 100, 'h' => 100]);
        }

        return $data;
    }

    public function getArrayForModel(TwillModelContract $model): array
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
