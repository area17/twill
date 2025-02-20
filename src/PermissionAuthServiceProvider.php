<?php

namespace A17\Twill;

use A17\Twill\Enums\PermissionLevel;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
use A17\Twill\Facades\TwillPermissions;

class PermissionAuthServiceProvider extends ServiceProvider
{
    protected static $cache = [];

    protected static $useCache = true;

    /**
     * Disable gate caching for integration tests
     */
    public static function disableCache()
    {
        self::$useCache = false;
    }

    /**
     * For compatibility with legacy AuthServiceProvider
     */
    protected function define($ability, $callback)
    {
        Gate::define($ability, $callback);
    }

    protected function authorize($user, $callback, $moduleName = null)
    {
        if ($user->is_superadmin) {
            return true;
        }

        if (!$user->published) {
            return false;
        }

        return $callback($user);
    }

    public function boot()
    {
        /***
         *
         *    Global permissions
         *
         ***/

        $this->define('edit-settings', function ($user) {
            return $this->authorize($user, function ($user) {
                return $user->role->permissions()->global()->where('name', 'edit-settings')->exists();
            });
        });

        $this->define('edit-users', function ($user) {
            return $this->authorize($user, function ($user) {
                return $user->role->permissions()->global()->where('name', 'edit-users')->exists();
            });
        });

        $this->define('edit-user', function ($user, $editedUser) {
            return $this->authorize($user, function ($user) use ($editedUser) {
                return ($user->id === $editedUser->id)
                    || ($editedUser->role->position >= $user->role->position && $user->can('edit-users'));
            });
        });

        $this->define('edit-user-roles', function ($user) {
            return $this->authorize($user, function ($user) {
                return $user->role->permissions()->global()->where('name', 'edit-user-roles')->exists();
            });
        });

        $this->define('edit-role', function ($user, $editedRole) {
            return $this->authorize($user, function ($user) use ($editedRole) {
                return ($editedRole->position >= $user->role->position) && $user->can('edit-user-roles');
            });
        });

        $this->define('edit-user-groups', function ($user) {
            if (
                !TwillPermissions::levelIsOneOf([
                PermissionLevel::LEVEL_ROLE_GROUP,
                PermissionLevel::LEVEL_ROLE_GROUP_ITEM,
                ])
            ) {
                return false;
            }

            return $this->authorize($user, function ($user) {
                return $user->role->permissions()->global()->where('name', 'edit-user-groups')->exists();
            });
        });

        $this->define('edit-group', function ($user, $editedGroup) {
            return $this->authorize($user, function ($user) use ($editedGroup) {
                return !$editedGroup->isEveryoneGroup() && $user->can('edit-user-groups');
            });
        });

        $this->define('access-user-management', function ($user) {
            return $this->authorize($user, function ($user) {
                return $user->can('edit-users') || $user->can('edit-user-roles') || $user->can('edit-user-groups');
            });
        });

        $this->define('manage-modules', function ($user) {
            if (self::$useCache && isset(self::$cache['manage-modules'])) {
                return self::$cache['manage-modules'];
            }

            return self::$cache['manage-modules'] = $this->authorize($user, function ($user) {
                return $user->role->permissions()->global()->where('name', 'manage-modules')->exists()
                    || isUserGroupPermissionModuleExists($user, 'global', 'manage-modules');
            });
        });

        $this->define('access-media-library', function ($user) {
            return $this->authorize($user, function ($user) {
                return $user->can('edit-media-library')
                    || $user->role->permissions()->global()->where('name', 'access-media-library')->exists();
            });
        });

        $this->define('edit-media-library', function ($user) {
            return $this->authorize($user, function ($user) {
                return $user->role->permissions()->global()->where('name', 'edit-media-library')->exists();
            });
        });

        $this->define('impersonate', function ($user) {
            return $this->authorize($user, function ($user) {
                return $user->is_superadmin;
            });
        });

        /***
         *
         *    Module permissions
         *
         ***/

        $this->define('access-module-list', function ($user, $moduleName) {
            if (self::$useCache && isset(self::$cache['access-module-list-' . $moduleName])) {
                return self::$cache['access-module-list-' . $moduleName];
            }

            return self::$cache['access-module-list-' . $moduleName] = $this->authorize(
                $user,
                function ($user) use ($moduleName) {
                    return $user->can('view-module', $moduleName)
                        || $user->allPermissions()->ofModuleName($moduleName)->exists();
                }
            );
        });

        // The gate of accessing module list page,
        $this->define('view-module', function ($user, $moduleName) {
            if (self::$useCache && isset(self::$cache['view-module-' . $moduleName])) {
                return self::$cache['view-module-' . $moduleName];
            }

            return self::$cache['view-module-' . $moduleName] = $this->authorize(
                $user,
                function ($user) use ($moduleName) {
                    return $user->can('edit-module', $moduleName)
                        || $user->role->permissions()->ofModuleName($moduleName)->where('name', 'view-module')->exists()
                        || isUserGroupPermissionModuleExists($user, $moduleName, 'view-module');
                }
            );
        });

        $this->define('edit-module', function ($user, $moduleName) {
            if (self::$useCache && isset(self::$cache['edit-module-' . $moduleName])) {
                return self::$cache['edit-module-' . $moduleName];
            }

            return self::$cache['edit-module-' . $moduleName] = $this->authorize(
                $user,
                function ($user) use ($moduleName) {
                    return $user->can('manage-module', $moduleName)
                        || $user->role->permissions()
                            ->module()
                            ->ofModuleName($moduleName)
                            ->where('name', 'edit-module')
                            ->exists()
                        || isUserGroupPermissionModuleExists($user, $moduleName, 'edit-module');
                }
            );
        });

        $this->define('manage-module', function ($user, $moduleName) {
            if (self::$useCache && isset(self::$cache['manage-module-' . $moduleName])) {
                return self::$cache['manage-module-' . $moduleName];
            }

            return self::$cache['manage-module-' . $moduleName] = $this->authorize(
                $user,
                function ($user) use ($moduleName) {
                    if (!TwillPermissions::getPermissionModule($moduleName)) {
                        return true;
                    }

                    return $user->can('manage-modules')
                        || $user->role->permissions()->module()->ofModuleName($moduleName)->where(
                            'name',
                            'manage-module'
                        )->exists()
                        || isUserGroupPermissionModuleExists($user, $moduleName, 'manage-module');
                }
            );
        });

        /***
         *
         *    Module item permissions
         *
         ***/

        $this->define('view-item', function ($user, $item) {
            $key = 'view-item-' . str_replace("\\", "-", get_class($item)) . '-' . $item->id;
            if (self::$useCache && isset(self::$cache[$key])) {
                return self::$cache[$key];
            }

            return self::$cache[$key] = $this->authorize($user, function ($user) use ($item) {
                return $item->public
                    || $user->can('edit-item', $item)
                    || $user->can('view-module', getModuleNameByModel(get_class($item)))
                    || $user->permissions()->ofItem($item)->where('name', 'view-item')->exists()
                    || isUserGroupPermissionItemExists($user, $item, 'view-item');
            });
        });

        $this->define('edit-item', function ($user, $item) {
            $key = 'edit-item-' . str_replace("\\", "-", get_class($item)) . '-' . $item->id;
            if (self::$useCache && isset(self::$cache[$key])) {
                return self::$cache[$key];
            }

            return self::$cache[$key] = $this->authorize($user, function ($user) use ($item) {
                return $user->can('manage-item', $item)
                    || $user->can('edit-module', getModuleNameByModel(get_class($item)))
                    || $user->permissions()->ofItem($item)->where('name', 'edit-item')->exists()
                    || isUserGroupPermissionItemExists($user, $item, 'edit-item');
            });
        });

        $this->define('manage-item', function ($user, $item) {
            $key = 'manage-item-' . str_replace("\\", "-", get_class($item)) . '-' . $item->id;
            if (self::$useCache && isset(self::$cache[$key])) {
                return self::$cache[$key];
            }

            return self::$cache[$key] = $this->authorize($user, function ($user) use ($item) {
                return $user->can('manage-module', getModuleNameByModel(get_class($item)))
                    || $user->permissions()->ofItem($item)->where('name', 'manage-item')->exists()
                    || isUserGroupPermissionItemExists($user, $item, 'manage-item');
            });
        });
    }
}
