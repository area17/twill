<?php

namespace A17\Twill\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
 
class ModuleDestroy
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Name of the module.
     *
     * @var string
     */
    public $module;
 
    /**
     * The repository instance.
     *
     * @var \A17\Twill\Repositories\ModuleRepository
     */
    public $repository;

    /**
     * The module IDs.
     *
     * @var array
     */
    public $ids;

    /**
     * Type of destroy (single or bulk).
     *
     * @var array
     */
    public $type;

    /**
     * Create a new event instance.
     *
     * @param string $module
     * @param \A17\Twill\Repositories\ModuleRepository  $repository
     * @param array $ids
     * @param string $type
     * @return void
     */
    public function __construct($module, $repository, $ids, $type = 'single')
    {
        $this->module = $module;
        $this->repository = $repository;
        $this->ids = array_values($ids);
        $this->type = $type;
    }
}