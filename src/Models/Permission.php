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

    public function permissionable()
    {
        return $this->morphTo();
    }

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

    public static function permissionable_modules()
    {
        return getAllModules();
    }
}
