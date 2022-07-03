<?php

namespace A17\Twill\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
 
class UserLogin
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * User model.
     *
     * @var A17\Twill\Models\User
     */
    public $user;
 
    /**
     * Create a new event instance.
     *
     * @param A17\Twill\Models\User $user
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
    }
}