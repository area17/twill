<?php

namespace A17\Twill;

use A17\Twill\Models\Enums\UserRole;
use A17\Twill\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    const SUPERADMIN = 'SUPERADMIN';

    protected function authorize($user, $callback)
    {
        if (!$user->isPublished()) {
            return false;
        }

        if ($user->isSuperAdmin()) {
            return true;
        }

        return $callback($user);
    }

    protected function userHasRole($user, $roles)
    {
        return in_array($user->role_value, $roles);
    }

    public function boot()
    {
        Gate::define('list', function ($user) {
            return $this->authorize($user, function ($user) {
                return $this->userHasRole($user, [UserRole::VIEWONLY, UserRole::PUBLISHER, UserRole::ADMIN]);
            });
        });

        Gate::define('edit', function ($user) {
            return $this->authorize($user, function ($user) {
                return $this->userHasRole($user, [UserRole::PUBLISHER, UserRole::ADMIN]);
            });
        });

        Gate::define('reorder', function ($user) {
            return $this->authorize($user, function ($user) {
                return $this->userHasRole($user, [UserRole::PUBLISHER, UserRole::ADMIN]);
            });
        });

        Gate::define('publish', function ($user) {
            return $this->authorize($user, function ($user) {
                return $this->userHasRole($user, [UserRole::PUBLISHER, UserRole::ADMIN]);
            });
        });

        Gate::define('feature', function ($user) {
            return $this->authorize($user, function ($user) {
                return $this->userHasRole($user, [UserRole::PUBLISHER, UserRole::ADMIN]);
            });
        });

        Gate::define('delete', function ($user) {
            return $this->authorize($user, function ($user) {
                return $this->userHasRole($user, [UserRole::PUBLISHER, UserRole::ADMIN]);
            });
        });

        Gate::define('upload', function ($user) {
            return $this->authorize($user, function ($user) {
                return $this->userHasRole($user, [UserRole::PUBLISHER, UserRole::ADMIN]);
            });
        });

        Gate::define('manage-users', function ($user) {
            return $this->authorize($user, function ($user) {
                return $this->userHasRole($user, [UserRole::ADMIN]);
            });
        });

        // As an admin, I can edit users, except superadmins
        // As a non-admin, I can edit myself only
        Gate::define('edit-user', function ($user, $editedUser = null) {
            return $this->authorize($user, function ($user) use ($editedUser) {
                $editedUserObject = User::find($editedUser);
                return ($this->userHasRole($user, [UserRole::ADMIN]) || $user->id == $editedUser)
                    && ($editedUserObject ? $editedUserObject->role !== self::SUPERADMIN : true);
            });
        });

        Gate::define('publish-user', function ($user) {
            return $this->authorize($user, function ($user) {
                $editedUserObject = User::find(request('id'));
                return $this->userHasRole($user, [UserRole::ADMIN]) && ($editedUserObject ? $user->id !== $editedUserObject->id && $editedUserObject->role !== self::SUPERADMIN : false);
            });
        });

        Gate::define('impersonate', function ($user) {
            return $user->role === self::SUPERADMIN;
        });

    }
}
