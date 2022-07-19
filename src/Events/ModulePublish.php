<?php

namespace A17\Twill\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
 
class ModulePublish
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
     * Published status.
     *
     * @var bool
     */
    public $published;

     /**
     * Type of publish (single or bulk).
     *
     * @var string
     */
    public $type;

    /**
     * Create a new event instance.
     *
     * @param string $module
     * @param \A17\Twill\Repositories\ModuleRepository  $repository
     * @param int $id
     * @param bool $published
     * @param string $type
     * @return void
     */
    public function __construct($module, $repository, $ids, $published, $type = 'single')
    {
        $this->module = $module;
        $this->repository = $repository;
        $this->ids = array_values($ids);
        $this->published = $published;
        $this->type = $type;
    }
}