<?php

namespace A17\Twill\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
 
class Impersonate
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Status of impersonating.
     *
     * @var bool
     */
    public $impersonate;
 
    /**
     * Create a new event instance.
     *
     * @param array $buckets
     * @return void
     */
    public function __construct($impersonate)
    {
        $this->impersonate = $impersonate;
    }
}