<?php

namespace A17\Twill;

use A17\Twill\Models\Enums\UserRole;
use A17\Twill\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    const SUPERADMIN = 'SUPERADMIN';

    public function boot()
    {
        Gate::before(function ($user, $ability) {
            if ($user->role === self::SUPERADMIN) {
                return true;
            }

            if (!$user->published) {
                return false;
            }
        });


        Gate::define('view-item', function ($user, $item) {
            return $user->itemPermission($item) && in_array($user->itemPermission($item)->guard_name, ["view", "edit", "manage"]);
        });

        Gate::define('edit-item', function ($user, $item) {
            return $user->itemPermission($item) && in_array($user->itemPermission($item)->guard_name, ["edit", "manage"]);
        });

        Gate::define('manage-item', function ($user, $item) {
            return $user->itemPermission($item) && in_array($user->itemPermission($item)->guard_name, ["manage"]);
        });


        Gate::define('list', function ($user) {
            return in_array($user->role_value, [UserRole::VIEWONLY, UserRole::PUBLISHER, UserRole::ADMIN]);
        });

        Gate::define('edit', function ($user) {
            return in_array($user->role_value, [UserRole::PUBLISHER, UserRole::ADMIN]);
        });

        Gate::define('reorder', function ($user) {
            return in_array($user->role_value, [UserRole::PUBLISHER, UserRole::ADMIN]);
        });

        Gate::define('publish', function ($user) {
            return in_array($user->role_value, [UserRole::PUBLISHER, UserRole::ADMIN]);
        });

        Gate::define('feature', function ($user) {
            return in_array($user->role_value, [UserRole::PUBLISHER, UserRole::ADMIN]);
        });

        Gate::define('delete', function ($user) {
            return in_array($user->role_value, [UserRole::PUBLISHER, UserRole::ADMIN]);
        });

        Gate::define('edit-user', function ($user, $editedUser =null) {
            $editedUserObject = User::find($editedUser);
            return ($user->can('edit') && in_array($user->role_value, [UserRole::ADMIN]) || $user->id == $editedUser)
                && ($editedUserObject ? $editedUserObject->role !== self::SUPERADMIN : true);
        });

        Gate::define('edit-user-role', function ($user) {
            return in_array($user->role_value, [UserRole::ADMIN]);
        });

        Gate::define('publish-user', function ($user) {
            $editedUserObject = User::find(request('id'));
            return $user->can('publish') && $user->id !== $editedUserObject->id && $editedUserObject->role !== self::SUPERADMIN;
        });

        Gate::define('impersonate', function ($user) {
            return $user->role === self::SUPERADMIN;
        });

    }
}
