<?php

namespace A17\Twill\Models;

use Illuminate\Database\Eloquent\Model as BaseModel;

class Permission extends BaseModel
{
    protected $fillable = [
        'name',
        'permissionable_type',
        'permissionable_id',
    ];

    public static function available($scope)
    {
        switch ($scope) {
            case 'global':
                return ['edit-settings', 'edit-users', 'edit-user-role', 'edit-user-groups', 'manage-modules', 'access-media-library'];
                break;
            case 'module':
                return ['view-module', 'edit-module', 'manage-module'];
                break;
            case 'item':
                return ['view-item', 'edit-item', 'manage-item'];
                break;
        }
    }

    public static function permissionableModules()
    {
        return getAllModules()->diff(collect(config('twill.permission.exclude_modules', [])));
    }

    public function permissionable()
    {
        return $this->morphTo();
    }

    // All users have this permission
    public function users()
    {
        return $this->belongsToMany('A17\Twill\Models\User', 'permission_twill_user', 'permission_id', 'twill_user_id');
    }

    // All roles have this permission
    public function roles()
    {
        return $this->belongsToMany('A17\Twill\Models\Role', 'permission_role', 'permission_id', 'role_id');
    }

    // All groups have this permission
    public function groups()
    {
        return $this->belongsToMany('A17\Twill\Models\Group', 'group_permission', 'permission_id', 'group_id');
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
        return $query->where([
            ['permissionable_type', get_class($item)],
            ['permissionable_id', $item->id],
        ]);
    }

    public function scopeOfModuleName($query, $moduleName)
    {
        return $query->where('permissionable_type', getModelByModuleName($moduleName));
    }

}
