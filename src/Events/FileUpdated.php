<?php

namespace A17\Twill\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
 
class FileUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The repository instance.
     *
     * @var \A17\Twill\Repositories\FileRepository
     */
    public $repository;

    /**
     * The module IDs.
     *
     * @var array
     */
    public $ids;

    /**
     * Tags submitted.
     *
     * @var array
     */
    public $tags;

    /**
     * Type of delete (single or bulk).
     *
     * @var array
     */
    public $type;

    /**
     * Create a new event instance.
     *
     * @param \A17\Twill\Models\File  $repository
     * @param array $ids
     * @param array $tags
     * @param string $type
     * @return void
     */
    public function __construct($repository, $ids, $tags = [], $type = 'single')
    {
        $this->repository = $repository;
        $this->ids = array_values($ids);
        $this->tags = array_values($tags);
        $this->type = $type;
    }
}