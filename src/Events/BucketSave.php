<?php

namespace A17\Twill\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
 
class BucketSave
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Buckets list.
     *
     * @var array
     */
    public $buckets;
 
    /**
     * Create a new event instance.
     *
     * @param array $buckets
     * @return void
     */
    public function __construct($buckets)
    {
        $this->buckets = $buckets;
    }
}