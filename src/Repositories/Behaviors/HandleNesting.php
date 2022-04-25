<?php

namespace A17\Twill\Repositories\Behaviors;

use A17\Twill\Jobs\ReorderNestedModuleItems;

trait HandleNesting
{
    /**
     * The Laravel queue name to be used for the reordering operation.
     *
     * @var string
     */
    protected $reorderNestedModuleItemsJobQueue = 'default';

    /**
     * @return \A17\Twill\Models\Model|null
     * @param mixed[] $with
     * @param mixed[] $withCount
     * @param mixed[] $scopes
     */
    public function forNestedSlug(string $nestedSlug, array $with = [], array $withCount = [], array $scopes = [])
    {
        $targetSlug = collect(explode('/', $nestedSlug))->last();

        $targetItem = $this->forSlug($targetSlug, $with, $withCount, $scopes);

        if (!$targetItem || $nestedSlug !== $targetItem->nestedSlug) {
            return null;
        }

        return $targetItem;
    }

    public function setNewOrder($ids)
    {
        ReorderNestedModuleItems::dispatch($this->model, $ids)
            ->onQueue($this->reorderNestedModuleItemsJobQueue);
    }
}
