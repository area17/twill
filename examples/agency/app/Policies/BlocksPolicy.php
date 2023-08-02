<?php

namespace App\Policies;

use A17\Twill\Models\Block;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BlocksPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(?User $user)
    {
        return true;
    }

    public function view(?User $user, Block $block)
    {
        return true;
    }

    public function viewRelatedItems(?User $user, Block $block)
    {
        return true;
    }

    public function viewMedia(?User $user, Block $block)
    {
        return true;
    }

    public function viewBlocks(?User $user, Block $block)
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        //
    }

    public function update(User $user, Block $block)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Block  $block
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Block $block)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Block  $block
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Block $block)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Block  $block
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Block $block)
    {
        //
    }
}
