<?php

namespace A17\Twill\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
 
class ModuleFeature
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
     * Featured status.
     *
     * @var bool
     */
    public $featured;

    /**
     * Type of feature (single or bulk).
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
    public function __construct($module, $repository, $ids, $featured, $type = 'single')
    {
        $this->module = $module;
        $this->repository = $repository;
        $this->ids = array_values($ids);
        $this->featured = $featured;
        $this->type = $type;
    }
}