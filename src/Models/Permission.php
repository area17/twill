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

    public function users()
    {
        return $this->belongsToMany('A17\Twill\Models\User', 'permission_twill_user', 'permission_id', 'twill_user_id');
    }

    public function roles()
    {
        return $this->belongsToMany('A17\Twill\Models\Role', 'permission_role', 'permission_id', 'role_id');
    }

    public function groups()
    {
        return $this->belongsToMany('A17\Twill\Models\Group', 'group_permission', 'permission_id', 'group_id');
    }
}
