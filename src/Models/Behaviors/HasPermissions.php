<?php

namespace A17\Twill\Models\Behaviors;

use A17\Twill\Models\Permission;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasPermissions
{
    /**
     * This method should be clean of checks, the checks should happen earlier on.
     *
     * @return BelongsToMany|Collection|Permission[]
     */
    public function permissions(): BelongsToMany
    {
        // Deal with the situation that twill user's table has been renamed.
        if (get_class($this) === twillModel('user')) {
            return $this->belongsToMany(
                Permission::class,
                'permission_twill_user',
                'twill_user_id',
                'permission_id'
            );
        }

        return $this->belongsToMany(Permission::class);
    }

    /**
     * Add global permission to item, after making sure the permission is
     * valid
     *
     * @param string $name
     * @return void
     */
    public function grantGlobalPermission($name)
    {
        $this->checkPermissionAvailable($name, Permission::SCOPE_GLOBAL);

        $permission = Permission::firstOrCreate(['name' => $name,]);

        // check the existence to avoid duplicate records on pivot table
        if (!$this->permissions()->global()->where('name', $name)->exists()) {
            $this->permissions()->save($permission);
        }
    }

    /**
     * Revoke global permission from the item, after making sure the permission is
     * valid
     *
     * @param string $name
     * @return void
     */
    public function revokeGlobalPermission($name)
    {
        $this->checkPermissionAvailable($name, Permission::SCOPE_GLOBAL);

        $this->permissions()->global()->detach(Permission::where('name', $name)->first()->id);
    }

    /**
     * Add module permission to item, after making sure the permission is
     * valid
     *
     * @param string $name
     * @param string|object $permissionableType
     * @return void
     */
    public function grantModulePermission($name, $permissionableType)
    {
        $this->checkPermissionAvailable($name, Permission::SCOPE_MODULE);

        $permission = Permission::firstOrCreate([
            'name' => $name,
            'permissionable_type' => $permissionableType,
        ]);

        $this->permissions()->save($permission);
    }

    /**
     * Revoke module permission from the item, after making sure the permission is
     * valid
     *
     * @param string $name
     * @param string|object $permissionableType
     * @return void
     */
    public function revokeModulePermission($name, $permissionableType)
    {
        $this->checkPermissionAvailable($name, Permission::SCOPE_MODULE);
        $permission = Permission::ofModel($permissionableType)->where('name', $name)->first();
        if ($permission !== null) {
            $this->permissions()->module()->detach($permission->id);
        }
    }

    /**
     * Revoke all module permissions from the item
     *
     * @param string|object $permissionableType
     * @return void
     */
    public function revokeAllModulePermission($permissionableType)
    {
        foreach (Permission::ofModel($permissionableType)->get() as $permission) {
            $this->permissions()->module()->detach($permission->id);
        }
    }

    /**
     * Add module item permission, after making sure the permission is
     * valid
     *
     * @param string $name
     * @param object $permissionableItem
     * @return void
     */
    public function grantModuleItemPermission($name, $permissionableItem)
    {
        // First find or create the corresponding permission
        // If the object haven't been given this permission, give it
        // If the object already had this permission, skip it
        $this->checkPermissionAvailable($name, Permission::SCOPE_ITEM);

        $permission = Permission::firstOrCreate([
            'name' => $name,
            'permissionable_type' => $permissionableItem ? get_class($permissionableItem) : null,
            'permissionable_id' => $permissionableItem ? $permissionableItem->id : null,
        ]);

        $this->revokeModuleItemAllPermissions($permissionableItem);
        $this->permissions()->attach($permission->id);
    }

    /**
     * Revoke module item permissions, after making sure the permission is
     * valid
     *
     * @param string $name
     * @param object $permissionableItem
     * @return void
     */
    public function revokeModuleItemPermission($name, $permissionableItem)
    {
        $this->checkPermissionAvailable($name, Permission::SCOPE_ITEM);

        $permission = Permission::ofItem($permissionableItem)->where('name', $name)->first();
        if ($permission !== null) {
            $this->permissions()->ofItem($permissionableItem)->detach($permission->id);
        }
    }

    /**
     * Revoke all module item permissions
     *
     * @param object $permissionableItem
     * @return void
     */
    public function revokeModuleItemAllPermissions($permissionableItem)
    {
        $this->removePermissions(Permission::ofItem($permissionableItem)->pluck('id')->toArray());
    }

    /**
     * Revoke all permissions
     *
     * @param object $permissionableItem
     * @return void
     */
    public function revokeAllPermissions()
    {
        $this->removePermissions($this->permissions->pluck('id')->toArray());
    }

    /**
     * Revoke all permissions from a list of permission ids
     *
     * @param int[] $permissionableIds
     * @return void
     */
    public function removePermissions($permissionableIds)
    {
        if (!empty($permissionableIds)) {
            $this->permissions()->detach($permissionableIds);
        }
    }

    /**
     * Check if a permission is available for a particular scope
     *
     * @param string $name
     * @param string $scope
     * @return void
     * @see Permission::SCOPE_GLOBAL
     * @see Permission::SCOPE_MODULE
     * @see Permission::SCOPE_ITEM
     *
     */
    protected function checkPermissionAvailable($name, $scope)
    {
        if (!in_array($name, Permission::available($scope))) {
            abort(400, 'Operation failed, permission ' . $name . ' not available on ' . $scope);
        }
    }
}
