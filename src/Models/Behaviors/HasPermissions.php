<?php

namespace A17\Twill\Models\Behaviors;

use A17\Twill\Models\Permission;

trait HasPermissions
{
    public function grantGlobalPermission($name)
    {
        $available_permissions = Permission::available('global');
        if (in_array($name, $available_permissions)) {
            $permission = Permission::firstOrCreate([
                'name' => $name,
            ]);
            if (!$this->permissions()->global()->where('name', $name)->exists()) {
                $this->permissions()->save($permission);
            }
        } else {
            abort(400, 'grant failed, permission not available on global');
        }
    }

    public function grantModulePermission($name, $permissionableType)
    {
        $available_permissions = Permission::available('module');
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
        $available_permissions = Permission::available('item');
        if (in_array($name, $available_permissions)) {
            $permission = Permission::firstOrCreate([
                'name' => $name,
                'permissionable_type' => $permissionableItem ? get_class($permissionableItem) : null,
                'permissionable_id' => $permissionableItem ? $permissionableItem->id : null,
            ]);
            if (!$this->permissions()->ofItem($permissionableItem)->pluck('name')->contains($name)) {
                $this->permissions()->moduleItem()->save($permission);
            }
        } else {
            abort(400, 'grant failed, permission not available on item');
        }
    }

    public function revokeGlobalPermission($name)
    {
        $available_permissions = Permission::available('global');
        if (in_array($name, $available_permissions)) {
            $this->permissions()->global()->detach(Permission::where('name', $name)->first()->id);
        } else {
            abort(400, 'revoke failed, permission not available on global');
        }
    }

    public function revokeModulePermission($name, $permissionableType)
    {
        $available_permissions = Permission::available('module');
        if (in_array($name, $available_permissions)) {

        } else {
            abort(400, 'revoke failed, permission not available on module');
        }
    }

    public function revokeModuleItemPermission($name, $permissionableItem)
    {
        $this->permissions()->ofItem($permissionableItem)->detach(Permission::where('name', $name)->first()->id);
    }

    public function revokeModuleItemAllPermissions($permissionableItem)
    {
        $this->permissions()->ofItem($permissionableItem)->detach();
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

    public function userAllPermissions()
    {
        if (get_class($this) === 'A17\Twill\Models\User') {
            $permissions = Permission::whereHas('users', function ($query) {
                $query->where('id', $this->id);
            })->orWhereHas('roles', function ($query) {
                $query->where('id', $this->role->id);
            })->orWhereHas('groups', function ($query) {
                $query->whereIn('id', $this->groups()->pluck('id'));
            });
            return $permissions;
        }
        // Has no effects for other models
        abort(500, "userAllPermissions function only allowed on User model");
    }

}
