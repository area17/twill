<?php

namespace A17\Twill\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
 
class MediaStored
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The repository instance.
     *
     * @var \A17\Twill\Models\File
     */
    public $repository;

    /**
     * Create a new event instance.
     *
     * @param string $module
     * @param \A17\Twill\Models\Media  $repository
     * @return void
     */
    public function __construct($repository)
    {
        $this->repository = $repository;
    }
}