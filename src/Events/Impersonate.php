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
     * Admin user instance.
     *
     * @var \Illuminate\Auth\AuthManager
     */
    public $admin;


    /**
     * ID of user which admin impersonates.
     *
     * @var int
     */
    public $id;
 
    /**
     * Create a new event instance.
     *
     * @param bool $impersonate
     * @param \Illuminate\Auth\AuthManager $admin
     * @param int $id
     * @return void
     */
    public function __construct($impersonate, $admin, $id)
    {
        $this->impersonate = $impersonate;
        $this->admin = $admin;
        $this->id = $id;
    }
}