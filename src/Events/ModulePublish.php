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
     * The module ID.
     *
     * @var int
     */
    public $id;

    /**
     * The module ID.
     *
     * @var bool
     */
    public $published;

    /**
     * Create a new event instance.
     *
     * @param string $module
     * @param \A17\Twill\Repositories\ModuleRepository  $repository
     * @param int $id
     * @param bool $published
     * @return void
     */
    public function __construct($module, $repository, $id, $published)
    {
        $this->module = $module;
        $this->repository = $repository;
        $this->id = $id;
        $this->published = $published;
    }
}