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
            return false;
        });

        Gate::define('edit-users', function ($user) {
            return $user->role->globalPermissions()->where('name', 'edit-users')->exists();
        });

        // Deprecated, use edit-users instead
        // Gate::define('edit-user', function ($user, $editedUser = null) {
        //     return false;
        //     $editedUserObject = User::find($editedUser);
        //     return ($user->can('edit') && in_array($user->role_value, [UserRole::OWNER]) || $user->id == $editedUser)
        //         && ($editedUserObject ? $editedUserObject->role !== self::SUPERADMIN : true);
        // });

        Gate::define('edit-user-role', function ($user) {
            return $user->role->globalPermissions()->where('name', 'edit-user-role')->exists();
        });

        Gate::define('edit-user-groups', function ($user) {
            return $user->role->globalPermissions()->where('name', 'edit-user-groups')->exists();
        });

        // Deprecated, use edit-users instead
        // Gate::define('publish-user', function ($user) {
        //     return false;
        // });

        Gate::define('access-user-management', function ($user) {
            return $user->can('edit-users') || $user->can('edit-user-roles') || $user->can('edit-user-groups');
        });

        Gate::define('manage-modules', function ($user) {
            return $user->role->globalPermissions()->where('name', 'manage-modules')->exists();
        });

        Gate::define('access-media-library', function ($user) {
            return $user->role->globalPermissions()->where('name', 'access-media-library')->exists();
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
        Gate::define('list', function ($user, $moduleName) {
            return $user->permissionsByModuleName($moduleName)->exists()
            || $user->role->permissionsByModuleName($moduleName)->exists()
            || $user->permissionsByModuleName($moduleName)->exists();
        });

        Gate::define('edit', function ($user) {
            return false;
            // return in_array($user->role_value, [UserRole::OWNER, UserRole::TEAM]);
        });

        Gate::define('reorder', function ($user) {
            return false;
            // return in_array($user->role_value, [UserRole::OWNER]);
        });

        Gate::define('publish', function ($user) {
            return false;
            // return in_array($user->role_value, [UserRole::OWNER]);
        });

        Gate::define('feature', function ($user) {
            return false;
            // return in_array($user->role_value, [UserRole::OWNER]);
        });

        Gate::define('delete', function ($user) {
            return false;
            // return in_array($user->role_value, [UserRole::OWNER]);
        });

        /***
         *
         *    Module item permissions
         *
         ***/

        Gate::define('view-item', function ($user, $item) {
            return false;
            // return in_array($user->permissionNameByItem($item), ["view", "edit", "manage"]) || in_array($user->role_value, [UserRole::OWNER]);
        });

        Gate::define('edit-item', function ($user, $item) {
            return false;
            // return in_array($user->permissionNameByItem($item), ["edit", "manage"]) || in_array($user->role_value, [UserRole::OWNER]);
        });

        Gate::define('manage-item', function ($user, $item) {
            return false;
            // return in_array($user->permissionNameByItem($item), ["manage"]) || in_array($user->role_value, [UserRole::OWNER]);
        });

        Gate::define('delete-item', function ($user, $item) {
            return false;
            // return in_array($user->permissionNameByItem($item), ["edit", "manage"]) || in_array($user->role_value, [UserRole::OWNER]);
        });

    }
}
