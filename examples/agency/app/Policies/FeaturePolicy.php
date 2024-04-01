<?php

namespace App\Policies;

use A17\Twill\Models\Feature;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FeaturePolicy
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

    public function viewFeatured(?User $user, Feature $feature)
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Feature  $feature
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(?User $user, Feature $feature)
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

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Feature  $feature
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Feature $feature)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Feature  $feature
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Feature $feature)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Feature  $feature
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Feature $feature)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Feature  $feature
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Feature $feature)
    {
        //
    }
}
