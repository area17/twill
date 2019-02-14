<?php

namespace A17\Twill\Models;

use Illuminate\Database\Eloquent\Model as BaseModel;

class Permission extends BaseModel
{
    public static $available = [
        'global' => ['edit-settings', 'edit-users', 'edit-user-role', 'edit-user-groups', 'manage-modules', 'access-media-library'],
        'module' => ['list', 'reorder', 'create', 'feature'],
        'item' => ['view', 'publish', 'edit', 'delete', 'manage'],
    ];

    protected $fillable = [
        'name',
        'permissionable_type',
        'permissionable_id',
    ];

    public function permissionable()
    {
        return $this->morphTo();
    }
}
