<?php

namespace App\Http\Controllers\Twill;

use A17\Twill\Http\Controllers\Admin\ModuleController;

class CategoryController extends ModuleController
{
    protected $moduleName = 'categories';

    protected $indexOptions = [
        'reorder' => true,
    ];

    protected function indexData($request)
    {
        return [
            'nested' => true,
            'nestedDepth' => 2, // this controls the allowed depth in UI
        ];
    }

    protected function transformIndexItems($items)
    {
        return $items->toTree();
    }

    protected function indexItemData($item)
    {
        return $item->children
            ? [
                'children' => $this->getIndexTableData($item->children),
            ]
            : [];
    }

    protected function getBrowserItems($scopes = [])
    {
        return $this->repository->get(
            $this->indexWith,
            $scopes,
            $this->orderScope(),
            request('offset') ?? $this->perPage ?? 50,
            true
        );
    }
}
