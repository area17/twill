<?php

namespace A17\CmsToolkit;

use A17\CmsToolkit\Models\Enums\UserRole;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{

    public function boot()
    {
        Gate::before(function ($user, $ability) {
            if ($user->role === 'SUPERADMIN') {
                return true;
            }

            if (!$user->published) {
                return false;
            }
        });

        Gate::define('list', function ($user) {
            return in_array($user->role_value, [UserRole::VIEWONLY, UserRole::PUBLISHER, UserRole::ADMIN]);
        });

        Gate::define('edit', function ($user) {
            return in_array($user->role_value, [UserRole::PUBLISHER, UserRole::ADMIN]);
        });

        Gate::define('sort', function ($user) {
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

        Gate::define('edit-user', function ($user, $editedUser) {
            return $user->can('edit') || $user->id == $editedUser;
        });

        Gate::define('edit-user-role', function ($user) {
            return in_array($user->role_value, [UserRole::ADMIN]);
        });

        Gate::define('impersonate', function ($user) {
            return $user->role === 'SUPERADMIN';
        });

    }
}
