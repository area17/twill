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
     * @param string $nestedSlug
     * @param array $with
     * @param array $withCount
     * @param array $scopes
     * @return \A17\Twill\Models\Model|null
     */
    public function forNestedSlug($nestedSlug, $with = [], $withCount = [], $scopes = [])
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
        ReorderNestedModuleItems::dispatch($this->model, $ids, auth()->user())
            ->onQueue($this->reorderNestedModuleItemsJobQueue);
    }

    /**
     * @param \A17\Twill\Models\Model $object
     * @return void
     */
    public function afterRestore($object)
    {
        if (!$object->parent) {
            $object->parent_id = null;
            $object->save();
        }
    }
}
