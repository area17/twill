<?php

namespace A17\Twill\Models\Behaviors;

use A17\Twill\Models\Permission;

trait HasPermissions
{
    public function grantGlobalPermission($name)
    {
        $available_permissions = Permission::$available['global'];
        if (in_array($name, $available_permissions)) {
            $permission = Permission::where([
                'name' => $name,
            ])->first();
            $this->permissions()->save($permission);
        } else {
            abort(400, 'Permission not available on global');
        }
    }

    public function grantModulePermission($name, $permissionableType)
    {
        $available_permissions = Permission::$available['module'];
        if (in_array($name, $available_permissions)) {
            $permission = Permission::firstOrCreate([
                'name' => $name,
                'permissionable_type' => $permissionableType,
            ]);
            $this->permissions()->save($permission);
        } else {
            abort(400, 'Permission not available on module');
        }
    }

    public function grantModuleItemPermission($name, $permissionableItem = null)
    {
        $available_permissions = Permission::$available['item'];
        if (in_array($name, $available_permissions)) {
            $permission = Permission::firstOrCreate([
                'name' => $name,
                'permissionable_type' => $permissionableItem ? get_class($permissionableItem) : null,
                'permissionable_id' => $permissionableItem ? $permissionableItem->id : null,
            ]);
            $this->permissions()->save($permission);
        } else {
            abort(400, 'Permission not available on item');
        }
    }

    public function permissions()
    {
        // Deal with the situation that twill user's table has been renamed.
        if (get_class($this) === 'A17\Twill\Models\User') {
            return $this->belongsToMany('A17\Twill\Models\Permission', 'permission_twill_user', 'twill_user_id', 'permission_id');
        } else {
            return $this->belongsToMany('A17\Twill\Models\Permission');
        }
    }

    public function globalPermissions()
    {
        return $this->permissions()->whereNull(['permissionable_type', 'permissionable_id']);
    }

    public function itemPermissions()
    {
        return $this->permissions()->whereNotNull('permissionable_type')->whereNotNull('permissionable_id');
    }

    public function modulePermissions()
    {
        return $this->permissions()->whereNotNull('permissionable_type')->whereNull('permissionable_id');
    }

    public function permissionByItem($item)
    {
        return $this->permissions()->where([
            ['permissionable_type', get_class($item)],
            ['permissionable_id', $item->id],
        ])->first();
    }

    public function permissionNameByItem($item)
    {
        return $this->permissionByItem($item) ? $this->permissionByItem($item)->name : null;
    }
}
