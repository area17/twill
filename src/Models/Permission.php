<?php

namespace A17\Twill\Models;

use A17\Twill\Models\Role;
use A17\Twill\Models\Group;
use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Support\Str;

class Permission extends BaseModel
{
    protected $fillable = [
        'name',
        'permissionable_type',
        'permissionable_id',
        'is_default'
    ];

    protected $appends = ['permissionable_module'];

    public static function available($scope)
    {
        switch ($scope) {
            case 'global':
                return ['edit-settings', 'edit-users', 'edit-user-role', 'edit-user-groups', 'manage-modules', 'access-media-library'];
                break;
            case 'module':
                return array_merge(['view-module', 'edit-module'], (config('twill.permission.level')=='roleGroupModule' ? ['manage-module'] : []));
                break;
            case 'item':
                return ['view-item', 'edit-item', 'manage-item'];
                break;
        }
    }

    public static function permissionableModules()
    {
        $config = config('twill.permission.modules');
        // when use permissions for multi-tenants project
        if (config('twill.support_subdomain_admin_routing')
            && count($config) > 0 
            && is_array(array_values($config)[0])
            ) {
                return collect($config[config('twill.active_subdomain')]);
        }
        return collect($config);
    }

    public static function permissionableParentModuleItems()
    {
        return self::permissionableModules()->filter(function($module) {
            return !strpos($module, '.');
        })->mapWithKeys(function ($module) {
            return [$module => getRepositoryByModuleName($module)->get([], [], [], -1)];
        });
    }

    public function permissionable()
    {
        return $this->morphTo();
    }

    // All users have this permission
    public function users()
    {
        return $this->belongsToMany(twillModel('user'), 'permission_twill_user', 'permission_id', 'twill_user_id');
    }

    // All roles have this permission
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'permission_role', 'permission_id', 'role_id');
    }

    // All groups have this permission
    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_permission', 'permission_id', 'group_id');
    }

    // Global level permissions
    public function scopeGlobal($query)
    {
        return $query->whereNull('permissionable_type')->whereNull('permissionable_id');
    }

    // Module level permissions
    public function scopeModule($query)
    {
        return $query->whereNotNull('permissionable_type')->whereNull('permissionable_id');
    }

    // Item level permissions
    public function scopeModuleItem($query)
    {
        return $query->whereNotNull('permissionable_type')->whereNotNull('permissionable_id');
    }

    // All permissions towards a specific item
    public function scopeOfItem($query, $item)
    {
        $permissionableSubmodule = self::permissionableModules()->filter(function($module) use ($item){
            return strpos($module, '.') && explode('.', $module)[1] === getModuleNameByModel($item);
        })->first();

        if ($permissionableSubmodule) {
            $parentRelation = isset($item->parentRelation) ? $item->parentRelation : Str::singular(explode('.', $permissionableSubmodule)[0]);
            $item = $item->$parentRelation;
        }

        return $query->where([
            ['permissionable_type', get_class($item)],
            ['permissionable_id', $item->id],
        ]);
    }

    public function scopeOfModuleName($query, $moduleName)
    {
        // Submodule's permission will inherit from parent module
        if (strpos($moduleName, '.')) {
            $moduleName = explode('.', $moduleName)[0];
        }
        return $query->ofModel(getModelByModuleName($moduleName));
    }

    public function scopeOfModel($query, $model)
    {
        return $query->where('permissionable_type', $model);
    }

    public function getPermissionableModuleAttribute()
    {
        return getModuleNameByModel($this->permissionable_type);
    }

}
