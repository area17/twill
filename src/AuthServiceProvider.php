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

        Gate::define('edit-user-role', function ($user) {
            return $user->role->globalPermissions()->where('name', 'edit-user-role')->exists();
        });

        Gate::define('edit-user-groups', function ($user) {
            return $user->role->globalPermissions()->where('name', 'edit-user-groups')->exists();
        });

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
            return $user->can('manage-modules')
            || $user->role->permissionsByModuleName($moduleName)->exists()
            || $user->permissionsByModuleName($moduleName)->exists();
        });

        Gate::define('reorder', function ($user) {
            return $user->can('manage-modules');
            // return in_array($user->role_value, [UserRole::OWNER]);
        });

        Gate::define('publish', function ($user) {
            return $user->can('manage-modules');
            // return in_array($user->role_value, [UserRole::OWNER]);
        });

        Gate::define('feature', function ($user) {
            return $user->can('manage-modules');
            // return in_array($user->role_value, [UserRole::OWNER]);
        });

        Gate::define('delete', function ($user) {
            return $user->can('manage-modules');
            // return in_array($user->role_value, [UserRole::OWNER]);
        });

        /***
         *
         *    Module item permissions
         *
         ***/

        Gate::define('view', function ($user, $item) {
            return $user->can('manage', $item) || $user->permissionsNameByItem($item)->where('name', 'view')->exists() || $user->group->permissionsNameByItem($item)->where('name', 'view')->exists();
        });

        Gate::define('publish', function ($user, $item) {
            return $user->can('manage', $item) || $user->permissionsNameByItem($item)->where('name', 'publish')->exists() || $user->group->permissionsNameByItem($item)->where('name', 'publish')->exists();
        });

        Gate::define('edit', function ($user, $item) {
            return $user->can('manage', $item) || $user->permissionsNameByItem($item)->where('name', 'edit')->exists() || $user->group->permissionsNameByItem($item)->where('name', 'edit')->exists();
        });

        Gate::define('delete', function ($user, $item) {
            return $user->can('manage', $item) || $user->permissionsNameByItem($item)->where('name', 'delete')->exists() || $user->group->permissionsNameByItem($item)->where('name', 'delete')->exists();
        });

        Gate::define('manage', function ($user, $item) {
            return $user->can('manage-modules') || $user->permissionsNameByItem($item)->where('name', 'manage')->exists() || $user->group->permissionsNameByItem($item)->where('name', 'manage')->exists();
        });

    }
}
