<?php

namespace A17\Twill\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
 
class ModuleCreate
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
     * Create a new event instance.
     *
     * @param string $module
     * @param \A17\Twill\Repositories\ModuleRepository  $repository
     * @return void
     */
    public function __construct($module, $repository)
    {
        $this->module = $module;
        $this->repository = $repository;
    }
}