<?php

namespace A17\Twill\Repositories\Behaviors;

use A17\Twill\Jobs\ReorderNestedModuleItems;
use A17\Twill\Models\Contracts\TwillModelContract;

trait HandleNesting
{
    /**
     * The Laravel queue name to be used for the reordering operation.
     */
    protected string $reorderNestedModuleItemsJobQueue = 'default';

    public function forNestedSlug(
        string $nestedSlug,
        array $with = [],
        array $withCount = [],
        array $scopes = []
    ): ?TwillModelContract {
        $targetSlug = collect(explode('/', $nestedSlug))->last();

        $targetItem = $this->forSlug($targetSlug, $with, $withCount, $scopes);

        if (!$targetItem || $nestedSlug !== $targetItem->nestedSlug) {
            return null;
        }

        return $targetItem;
    }

    public function setNewOrder(array $ids): void
    {
        ReorderNestedModuleItems::dispatch($this->model, $ids)
            ->onQueue($this->reorderNestedModuleItemsJobQueue);
    }

    public function afterRestore(TwillModelContract $object): void
    {
        if (!$object->parent) {
            $object->parent_id = null;
            $object->save();
        }
    }
}
