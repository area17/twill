<?php

namespace A17\Twill\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
 
class MediaUpdated
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
     * Data submitted.
     *
     * @var array
     */
    public $data;

    /**
     * Type of delete (single or bulk).
     *
     * @var array
     */
    public $type;

    /**
     * Create a new event instance.
     *
     * @param \A17\Twill\Models\Media  $repository
     * @param array $ids
     * @param array $data
     * @param string $type
     * @return void
     */
    public function __construct($repository, $ids, $data = [], $type = 'single')
    {
        $this->repository = $repository;
        $this->ids = array_values($ids);
        $this->data = $data;
        $this->type = $type;
    }
}