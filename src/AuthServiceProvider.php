<?php

namespace A17\Twill;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{

    public function boot()
    {
        Gate::before(function ($user, $ability) {
            if ($user->is_superadmin) {
                return true;
            }

            if (!$user->published) {
                return false;
            }
        });

        /***
         *
         *    Global permissions
         *
         ***/

        Gate::define('edit-settings', function ($user) {
            return $user->role->permissions()->global()->where('name', 'edit-settings')->exists();
        });

        Gate::define('edit-users', function ($user) {
            return $user->role->permissions()->global()->where('name', 'edit-users')->exists();
        });

        Gate::define('edit-user-role', function ($user) {
            return $user->role->permissions()->global()->where('name', 'edit-user-role')->exists();
        });

        Gate::define('edit-user-groups', function ($user) {
            return $user->role->permissions()->global()->where('name', 'edit-user-groups')->exists();
        });

        Gate::define('access-user-management', function ($user) {
            return $user->can('edit-users') || $user->can('edit-user-role') || $user->can('edit-user-groups');
        });

        Gate::define('manage-modules', function ($user) {
            return $user->role->permissions()->global()->where('name', 'manage-modules')->exists();
        });

        Gate::define('access-media-library', function ($user) {
            return $user->role->permissions()->global()->where('name', 'access-media-library')->exists();
        });

        Gate::define('impersonate', function ($user) {
            return $user->is_superadmin;
        });

        /***
         *
         *    Module permissions
         *
         ***/

        // The gate of access module list page.
        Gate::define('view-modules', function ($user, $moduleName) {
            return $user->can('edit-module', $moduleName)
            || $user->permissions()->ofModuleName($moduleName)->exists();
        });

        Gate::define('edit-module', function ($user, $moduleName) {
            return $user->can('manage-module', $moduleName)
            || $user->permissions()->ofModuleName($moduleName)->whereNull('permissionable_id')->where('name', 'manage-module')->exists();
        });

        Gate::define('manage-module', function ($user, $moduleName) {
            return $user->can('manage-modules')
            || $user->permissions()->ofModuleName($moduleName)->whereNull('permissionable_id')->where('name', 'manage-module')->exists();
        });

        /***
         *
         *    Module item permissions
         *
         ***/

        Gate::define('view-item', function ($user, $item) {
            return $user->can('edit-item', $item)
            || $user->permissions()->ofItem($item)->where('name', 'view-item')->exists()
            || $user->groups()->whereHas('permissions', function ($query) use ($item) {
                $query->where([
                    ['permissionable_type', get_class($item)],
                    ['permissionable_id', $item->id],
                    ['name', 'view-item'],
                ]);
            })->exists();
        });

        Gate::define('edit-item', function ($user, $item) {
            return $user->can('manage-item', $item)
            || $user->permissions()->ofItem($item)->where('name', 'edit-item')->exists()
            || $user->groups()->whereHas('permissions', function ($query) use ($item) {
                $query->where([
                    ['permissionable_type', get_class($item)],
                    ['permissionable_id', $item->id],
                    ['name', 'edit-item'],
                ]);
            })->exists();
        });

        Gate::define('manage-item', function ($user, $item) {
            return $user->can('manage-modules')
            || $user->permissions()->ofItem($item)->where('name', 'manage-item')->exists()
            || $user->groups()->whereHas('permissions', function ($query) use ($item) {
                $query->where([
                    ['permissionable_type', get_class($item)],
                    ['permissionable_id', $item->id],
                    ['name', 'manage-item'],
                ]);
            })->exists();
        });
    }
}
