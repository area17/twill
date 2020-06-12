<?php

namespace A17\Twill;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{

    protected function authorize($user, $callback, $moduleName = null)
    {   
        if ($user->is_superadmin) {
            return true;
        }
        
        if (!$user->published) {
            return false;
        }
        
        if ($moduleName && !isPermissionableModule($moduleName)) {
            return false;
        }

        return $callback($user);
    }

    protected static $cache = [];

    public function boot()
    {
        /***
         *
         *    Global permissions
         *
         ***/

        Gate::define('edit-settings', function ($user) {
            return $this->authorize($user, function ($user) {
                return $user->role->permissions()->global()->where('name', 'edit-settings')->exists();
            });
        });

        Gate::define('edit-users', function ($user) {
            return $this->authorize($user, function ($user) {
                return $user->role->permissions()->global()->where('name', 'edit-users')->exists();
            });
        });

        Gate::define('edit-user-role', function ($user) {
            return $this->authorize($user, function ($user) {
                return $user->role->permissions()->global()->where('name', 'edit-user-role')->exists();
            });
        });

        Gate::define('edit-user-groups', function ($user) {
            if (!in_array(Config::get('twill.permission.level'), ['roleGroup', 'roleGroupModule'])) {
                return false;
            }

            return $this->authorize($user, function ($user) {
                return $user->role->permissions()->global()->where('name', 'edit-user-groups')->exists();
            });
        });

        Gate::define('access-user-management', function ($user) {
            return $this->authorize($user, function ($user) {
                return $user->can('edit-users') || $user->can('edit-user-role') || $user->can('edit-user-groups');
            });
        });

        Gate::define('manage-modules', function ($user) {
            if (isset(self::$cache['manage-modules'])) {
                return self::$cache['manage-modules'];
            }
            return self::$cache['manage-modules'] = $this->authorize($user, function ($user) {
                return $user->role->permissions()->global()->where('name', 'manage-modules')->exists()
                || isUserGroupPermissionModuleExists($user, 'global', 'manage-modules');
            });
        });

        Gate::define('access-media-library', function ($user) {
            return $this->authorize($user, function ($user) {
                return $user->role->permissions()->global()->where('name', 'access-media-library')->exists();
            });
        });

        Gate::define('impersonate', function ($user) {
            return $this->authorize($user, function ($user) {
                return $user->is_superadmin;
            });
        });

        /***
         *
         *    Module permissions
         *
         ***/

        Gate::define('access-module-list', function ($user, $moduleName) {
            if (isset(self::$cache['access-module-list-' . $moduleName])) {
                return self::$cache['access-module-list-' . $moduleName];
            }

            return self::$cache['access-module-list-' . $moduleName] = $this->authorize($user, function ($user) use ($moduleName) {
                return $user->can('view-module', $moduleName)
                || $user->allPermissions()->ofModuleName($moduleName)->exists();
            }, $moduleName);
        });

        // The gate of accessing module list page,
        Gate::define('view-module', function ($user, $moduleName) {
            if (isset(self::$cache['view-module-' . $moduleName])) {
                return self::$cache['view-module-' . $moduleName];
            }

            return self::$cache['view-module-' . $moduleName] = $this->authorize($user, function ($user) use ($moduleName) {
                return $user->can('edit-module', $moduleName)
                || $user->role->permissions()->ofModuleName($moduleName)->where('name', 'view-module')->exists()
                || isUserGroupPermissionModuleExists($user, $moduleName, 'view-module');
            }, $moduleName);
        });

        Gate::define('edit-module', function ($user, $moduleName) {
            if (isset(self::$cache['edit-module-' . $moduleName])) {
                return self::$cache['edit-module-' . $moduleName];
            }
            return self::$cache['edit-module-' . $moduleName] = $this->authorize($user, function ($user) use ($moduleName) {
                return $user->can('manage-module', $moduleName)
                || $user->role->permissions()->module()->ofModuleName($moduleName)->where('name', 'edit-module')->exists()
                || isUserGroupPermissionModuleExists($user, $moduleName, 'edit-module');
            }, $moduleName);
        });

        Gate::define('manage-module', function ($user, $moduleName) {
            if (isset(self::$cache['manage-module-' . $moduleName])) {
                return self::$cache['manage-module-' . $moduleName];
            }

            return self::$cache['manage-module-' . $moduleName] = $this->authorize($user, function ($user) use ($moduleName) {
                return $user->can('manage-modules')
                || $user->role->permissions()->module()->ofModuleName($moduleName)->where('name', 'manage-module')->exists()
                || isUserGroupPermissionModuleExists($user, $moduleName, 'manage-module');
            }, $moduleName);
        });

        /***
         *
         *    Module item permissions
         *
         ***/

        Gate::define('view-item', function ($user, $item) {
            $key = 'view-item-' . str_replace("\\", "-", get_class($item)) . '-' . $item->id;
            if (isset(self::$cache[$key])) {
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

        Gate::define('edit-item', function ($user, $item) {
            $key = 'edit-item-' . str_replace("\\", "-", get_class($item)) . '-' . $item->id;
            if (isset(self::$cache[$key])) {
                return self::$cache[$key];
            }
            return self::$cache[$key] = $this->authorize($user, function ($user) use ($item) {
                return $user->can('manage-item', $item)
                || $user->can('edit-module', getModuleNameByModel(get_class($item)))
                || $user->permissions()->ofItem($item)->where('name', 'edit-item')->exists()
                || isUserGroupPermissionItemExists($user, $item, 'edit-item');
            });
        });

        Gate::define('manage-item', function ($user, $item) {
            $key = 'manage-item-' . str_replace("\\", "-", get_class($item)) . '-' . $item->id;
            if (isset(self::$cache[$key])) {
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
