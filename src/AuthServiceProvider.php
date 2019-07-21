<?php

namespace A17\Twill;

use A17\Twill\Models\Enums\UserRole;
use A17\Twill\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    const SUPERADMIN = 'SUPERADMIN';

    protected function userHasPermissions($user, $predicate)
    {
      if ($user->isSuperAdmin()) {
        return true;
      }

      if (! $user->isPublished()) {
        return false;
      }

      return $predicate($user);
    }

    protected function isRoleAllowed($user_role, $role_arr) {
      return in_array($user_role, $role_arr);
    }

    public function boot()
    {
        Gate::define('list', function ($user) {
          return $this->userHasPermissions($user, function($user) {
            return $this->isRoleAllowed($user->role_value, [UserRole::VIEWONLY, UserRole::PUBLISHER, UserRole::ADMIN]);
          });
        });

        Gate::define('edit', function ($user) {
          return $this->userHasPermissions($user, function($user) {
            return $this->isRoleAllowed($user->role_value, [UserRole::PUBLISHER, UserRole::ADMIN]);
          });
        });

        Gate::define('reorder', function ($user) {
            return $this->userHasPermissions($user, function($user) {
              return $this->isRoleAllowed($user->role_value, [UserRole::PUBLISHER, UserRole::ADMIN]);
            });
        });

        Gate::define('publish', function ($user) {
            return $this->userHasPermissions($user, function($user) {
              return $this->isRoleAllowed($user->role_value, [UserRole::VIEWONLY, UserRole::PUBLISHER, UserRole::ADMIN]);
            });
        });

        Gate::define('feature', function ($user) {
            return $this->userHasPermissions($user, function($user) {
              return $this->isRoleAllowed($user->role_value, [UserRole::PUBLISHER, UserRole::ADMIN]);
            });
        });

        Gate::define('delete', function ($user) {
            return $this->userHasPermissions($user, function($user) {
              return $this->isRoleAllowed($user->role_value, [UserRole::PUBLISHER, UserRole::ADMIN]);
            });
        });

        Gate::define('upload', function ($user) {
            return $this->userHasPermissions($user, function($user) {
              return $this->isRoleAllowed($user->role_value, [UserRole::PUBLISHER, UserRole::ADMIN]);
            });
        });

        Gate::define('edit-user', function ($user, $editedUser = null) {
            $editedUserObject = User::find($editedUser);
            return ($user->can('edit') || $user->id == $editedUser)
                && ($editedUserObject ? $editedUserObject->role !== self::SUPERADMIN : true);
        });

        Gate::define('edit-user-role', function ($user) {
            return $this->userHasPermissions($user, function($user) {
              return $this->isRoleAllowed($user->role_value, [UserRole::ADMIN]);
            });
        });

        Gate::define('publish-user', function ($user) {
            $editedUserObject = User::find(request('id'));
            return $user->can('publish') && ($editedUserObject ? $user->id !== $editedUserObject->id && $editedUserObject->role !== self::SUPERADMIN : false);
        });

        Gate::define('impersonate', function ($user) {
            return $user->role === self::SUPERADMIN;
        });

    }
}
