<?php

namespace A17\Twill\Http\Controllers\Admin;

abstract class NestedModuleController extends ModuleController
{
    /**
     * Indicates if only parent items should be displayed when browsing for this module
     * within a browser field.
     *
     * @var bool
     */
    protected $showOnlyParentItemsInBrowsers = true;

    /**
     * The maximum depth allowed for nested items. A value of `1` means parent & child.
     *
     * @var int
     */
    protected $nestedItemsDepth = 1;

    /**
     * @return bool[]|int[]
     */
    protected function indexData($request): array
    {
        return [
            'nested' => true,
            'nestedDepth' => $this->nestedItemsDepth,
        ];
    }

    protected function transformIndexItems($items)
    {
        return $items->toTree();
    }

    protected function indexItemData($item)
    {
        return ($item->children ? [
            'children' => $this->getIndexTableData($item->children),
        ] : []);
    }

    protected function getBrowserItems($scopes = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
    {
        if ($this->showOnlyParentItemsInBrowsers) {
            return $this->getIndexItems($scopes, true);
        }

        return $this->repository->get(
            $this->indexWith,
            $scopes,
            $this->orderScope(),
            request('offset') ?? $this->perPage ?? 50,
            true
        );
    }
}
