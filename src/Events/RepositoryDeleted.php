<?php

namespace A17\Twill\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
 
class RepositoryDeleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
 
    /**
     * The repository instance.
     *
     * @var \A17\Twill\Repositories\ModuleRepository
     */
    public $repository;

    /**
     * Create a new event instance.
     *
     * @param  Object  $repository
     * @return void
     */
    public function __construct($repository)
    {
        $this->repository = $repository;
    }
}