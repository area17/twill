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

    public function setNewOrder($ids)
    {
        ReorderNestedModuleItems::dispatch($this->model, $ids)
            ->onQueue($this->reorderNestedModuleItemsJobQueue);
    }
}
