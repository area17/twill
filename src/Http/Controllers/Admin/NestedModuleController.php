<?php

namespace A17\Twill\Http\Controllers\Admin;

use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

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

    protected function indexData($request)
    {
        return [
            'nested' => true,
            'nestedDepth' => $this->nestedItemsDepth,
        ];
    }

    protected function transformIndexItems(Collection|LengthAwarePaginator $items): Collection|LengthAwarePaginator
    {
        return $items->toTree();
    }

    protected function indexItemData($item)
    {
        return ($item->children ? [
            'children' => $this->getIndexTableData($item->children),
        ] : []);
    }

    protected function getBrowserItems($scopes = [])
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
