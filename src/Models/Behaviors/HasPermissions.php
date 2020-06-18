<?php

namespace A17\Twill\Models\Behaviors;

use A17\Twill\Models\Permission;

trait HasPermissions
{
    public function grantGlobalPermission($name)
    {
        $this->checkPermissionAvailable($name, 'global');
        $permission = Permission::firstOrCreate([
            'name' => $name,
        ]);
        //check the existence to avoid duplicate records on pivot table
        if (!$this->permissions()->global()->where('name', $name)->exists()) {
            $this->permissions()->save($permission);
        }
    }

    public function revokeGlobalPermission($name)
    {
        $this->checkPermissionAvailable($name, 'global');
        $this->permissions()->global()->detach(Permission::where('name', $name)->first()->id);
    }

    public function grantModulePermission($name, $permissionableType)
    {
        $this->checkPermissionAvailable($name, 'module');
        $permission = Permission::firstOrCreate([
            'name' => $name,
            'permissionable_type' => $permissionableType,
        ]);
        $this->permissions()->save($permission);

    }

    public function revokeModulePermission($name, $permissionableType)
    {
        $this->checkPermissionAvailable($name, 'module');
        $permission = Permission::ofModel($permissionableType)->where('name', $name)->first();
        if ($permission) {
            $this->permissions()->module()->detach($permission->id);
        }
    }

    public function revokeAllModulePermission($permissionableType)
    {
        foreach(Permission::ofModel($permissionableType)->get() as $permission) {
            $this->permissions()->module()->detach($permission->id);
        }
    }

    // First find or create the corresponding permission
    // If the object haven't been given this permission, give it
    // If the object already had this permission, skip it
    public function grantModuleItemPermission($name, $permissionableItem)
    {
        $this->checkPermissionAvailable($name, 'item');
        $permission = Permission::firstOrCreate([
            'name' => $name,
            'permissionable_type' => $permissionableItem ? get_class($permissionableItem) : null,
            'permissionable_id' => $permissionableItem ? $permissionableItem->id : null,
        ]);
        //avoid duplicate records on pivot table
        $this->revokeModuleItemAllPermissions($permissionableItem);
        $this->permissions()->attach($permission->id);
    }

    public function revokeModuleItemPermission($name, $permissionableItem)
    {
        $this->checkPermissionAvailable($name, 'item');
        $permission = Permission::ofItem($permissionableItem)->where('name', $name)->first();
        if ($permission) {
            $this->permissions()->ofItem($permissionableItem)->detach($permission->id);
        }
    }

    public function revokeModuleItemAllPermissions($permissionableItem)
    {
        $this->removePermissions(Permission::ofItem($permissionableItem)->pluck('id')->toArray());
    }

    public function revokeAllPermissions()
    {
        $this->removePermissions($this->permissions->pluck('id')->toArray());
    }

    public function removePermissions($permissionableIds)
    {
        if (!empty($permissionableIds)) {
            $this->permissions()->detach($permissionableIds);
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

    protected function checkPermissionAvailable($name, $scope)
    {
        if (!in_array($name, Permission::available($scope))) {
            abort(400, 'operation failed, permission ' . $name . ' not available on ' . $scope);
        }
    }

}
