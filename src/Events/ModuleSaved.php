<?php

namespace A17\Twill\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
 
class ModuleSaved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
 
    /**
     * The repository instance.
     *
     * @var \A17\Twill\Repositories\ModuleRepository
     */
    public $repository;

    /**
     * Additional data.
     *
     * @var array
     */
    public $fields;

    /**
     * Create a new event instance.
     *
     * @param  Object  $repository
     * @param array $data
     * @return void
     */
    public function __construct($repository, $fields = [])
    {
        $this->repository = $repository;
        $this->fields = $fields;
    }
}