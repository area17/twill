<?php

namespace A17\Twill\Models\Behaviors;

use A17\Twill\Facades\TwillPermissions;
use A17\Twill\Models\Permission;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait BaseTwillModelScopes
{
    public function scopePublished(Builder $query): Builder
    {
        return $query->where("{$this->getTable()}.published", true);
    }

    public function scopeAccessible(Builder $query): Builder
    {
        if (! TwillPermissions::enabled()) {
            return $query;
        }

        $model = get_class($query->getModel());
        $moduleName = TwillPermissions::getPermissionModule(getModuleNameByModel($model));

        if ($moduleName && ! Auth::user()->isSuperAdmin()) {
            // Get all permissions the logged in user has regards to the model.
            $allPermissions = Auth::user()->allPermissions();
            $allModelPermissions = (clone $allPermissions)->ofModel($model);

            // If the user has any module permissions, or global manage all modules permissions, all items will be return
            if (
                (clone $allModelPermissions)->module()
                    ->whereIn('name', Permission::available(Permission::SCOPE_MODULE))
                    ->exists()
                || (clone $allPermissions)->global()->where('name', 'manage-modules')->exists()
            ) {
                return $query;
            }

            // If the module is submodule, skip the scope.
            if (strpos($moduleName, '.')) {
                return $query;
            }

            $authorizedItemsIds = $allModelPermissions->moduleItem()->pluck('permissionable_id');

            return $query->whereIn($this->getTable() . '.id', $authorizedItemsIds);
        }

        return $query;
    }

    public function scopeDraft(Builder $query): Builder
    {
        return $query->where("{$this->getTable()}.published", false);
    }
}
