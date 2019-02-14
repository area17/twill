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
            abort(400, 'grant failed, permission not available on global');
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
            abort(400, 'grant failed, permission not available on module');
        }
    }

    // First find or create the corresponding permission
    // If the object doesn't have be given this permission, give it
    // If the object already have this permission, skip it
    public function grantModuleItemPermission($name, $permissionableItem)
    {
        $available_permissions = Permission::$available['item'];
        if (in_array($name, $available_permissions)) {
            $permission = Permission::firstOrCreate([
                'name' => $name,
                'permissionable_type' => $permissionableItem ? get_class($permissionableItem) : null,
                'permissionable_id' => $permissionableItem ? $permissionableItem->id : null,
            ]);
            if (!$this->permissionsNameByItem($permissionableItem)->contains($name)) {
                $this->itemPermissions()->save($permission);
            }
        } else {
            abort(400, 'grant failed, permission not available on item');
        }
    }

    public function revokeGlobalPermission($name)
    {
        $available_permissions = Permission::$available['global'];
        if (in_array($name, $available_permissions)) {

        } else {
            abort(400, 'revoke failed, permission not available on global');
        }
    }

    public function revokeModulePermission($name, $permissionableType)
    {
        $available_permissions = Permission::$available['module'];
        if (in_array($name, $available_permissions)) {

        } else {
            abort(400, 'revoke failed, permission not available on module');
        }
    }

    // Revoke all permissions for the item
    public function revokeModuleItemPermission($permissionableItem)
    {
        $this->itemPermissions()->detach();
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
        return $this->permissions()->whereNull('permissionable_type')->whereNull('permissionable_id');
    }

    public function itemPermissions()
    {
        return $this->permissions()->whereNotNull('permissionable_type')->whereNotNull('permissionable_id');
    }

    public function modulePermissions()
    {
        return $this->permissions()->whereNotNull('permissionable_type')->whereNull('permissionable_id');
    }

    public function permissionsByItem($item)
    {
        return $this->itemPermissions()->where([
            ['permissionable_type', get_class($item)],
            ['permissionable_id', $item->id],
        ]);
    }

    public function permissionsNameByItem($item)
    {
        return $this->permissionsByItem($item)->pluck('name');
    }
}
