<?php

namespace A17\Twill\Models;

use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Permission model
 *
 * @property-read string $permissionable_module
 * @method static Builder global() Get global scope permissions.
 * @method static Builder module() Get module scope permissions.
 * @method static Builder moduleItem() Get module item scope permissions.
 * @method static Builder ofItem(BaseModel $item) Get permissions related to an item.
 * @method static Builder ofModuleName(string $moduleName) Get permissions related to a Twill module.
 * @method static Builder ofModel(BaseModel $model) Get permissions related to a model.
 */
class Permission extends BaseModel
{
    /**
     * Constant that represents a list of permissions that belongs to the global scope.
     *
     * @var string
     * @see Permission::available($scope)
     */
    const SCOPE_GLOBAL = 'global';

    /**
     * Constant that represents a list of permissions that belongs to the module scope.
     *
     * @var string
     * @see Permission::available($scope)
     */
    const SCOPE_MODULE = 'module';

    /**
     * Constant that represents a list of permissions that belongs to the module item scope.
     *
     * @var string
     * @see Permission::available($scope)
     */
    const SCOPE_ITEM = 'item';

    protected $fillable = [
        'name',
        'permissionable_type',
        'permissionable_id',
        'is_default'
    ];

    protected $appends = ['permissionable_module'];

    /**
     * Return an array of permission names that belongs to
     * a certain scope (global, module or item).
     *
     * @param string $scope
     * @return string[]|void
     */
    public static function available($scope)
    {
        switch ($scope) {
            case Permission::SCOPE_GLOBAL:
                return ['edit-settings', 'edit-users', 'edit-user-role', 'edit-user-groups', 'manage-modules', 'access-media-library'];
                break;
            case Permission::SCOPE_MODULE:
                return array_merge(['view-module', 'edit-module'], (config('twill.permission.level')=='roleGroupModule' ? ['manage-module'] : []));
                break;
            case Permission::SCOPE_ITEM:
                return ['view-item', 'edit-item', 'manage-item'];
                break;
        }
    }

    /**
     * Retrieve the list of modules that permissions can be applied to.
     *
     * @return Collection
     */
    public static function permissionableModules()
    {
        return collect(config('twill.permission.modules', []));
    }

    /**
     * Retrieve a collection of items that belongs to keyed by permissionable module names.
     *
     * @return Collection
     */
    public static function permissionableParentModuleItems()
    {
        return self::permissionableModules()->filter(function ($module) {
            return !strpos($module, '.');
        })->mapWithKeys(function ($module) {
            return [$module => getRepositoryByModuleName($module)->get([], [], [], -1)];
        });
    }

    /**
     * Get the parent permissionable model (one of the permissionale module).
     *
     * @return MorphTo|Collection|User[]
     */
    public function permissionable()
    {
        return $this->morphTo();
    }

    /**
     * User model relationship
     *
     * @return BelongsToMany|Collection|User[]
     */
    public function users()
    {
        return $this->belongsToMany(twillModel('user'), 'permission_twill_user', 'permission_id', 'twill_user_id');
    }

    /**
     * Role model relationship
     *
     * @return BelongsToMany|Collection|BaseModel[]
     */
    public function roles()
    {
        return $this->belongsToMany(twillModel('role'), 'permission_role', 'permission_id', 'role_id');
    }

    /**
     * Group model relationship
     *
     * @return BelongsToMany|Collection|BaseModel[]
     */
    public function groups()
    {
        return $this->belongsToMany(twillModel('group'), 'group_permission', 'permission_id', 'group_id');
    }

    /**
     * Scope a query to only include global scope permissions.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeGlobal(Builder $query)
    {
        return $query->whereNull('permissionable_type')->whereNull('permissionable_id');
    }

    /**
     * Scope a query to only include module scope permissions.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeModule(Builder $query)
    {
        return $query->whereNotNull('permissionable_type')->whereNull('permissionable_id');
    }

    /**
     * Scope a query to only include module item scope permissions.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeModuleItem(Builder $query)
    {
        return $query->whereNotNull('permissionable_type')->whereNotNull('permissionable_id');
    }

    /**
     * Scope a query to only include permissions related to an item.
     *
     * @param Builder $query
     * @param BaseModel $item
     * @return Builder
     */
    public function scopeOfItem(Builder $query, BaseModel $item)
    {
        $permissionableSubmodule = self::permissionableModules()->filter(function($module) use ($item){
            return strpos($module, '.') && explode('.', $module)[1] === getModuleNameByModel($item);
        })->first();

        if ($permissionableSubmodule) {
            $parentRelation = isset($item->parentRelation) ? $item->parentRelation : Str::singular(explode('.', $permissionableSubmodule)[0]);
            $item = $item->$parentRelation;
        }

        return $query->where([
            ['permissionable_type', get_class($item)],
            ['permissionable_id', $item->id],
        ]);
    }

    /**
     * Scope a query to only include permissions related to a Twill module.
     *
     * @param Builder $query
     * @param string $moduleName
     * @return Builder
     */
    public function scopeOfModuleName(Builder $query, $moduleName)
    {
        // Submodule's permission will inherit from parent module
        if (strpos($moduleName, '.')) {
            $moduleName = explode('.', $moduleName)[0];
        }
        return $query->ofModel(getModelByModuleName($moduleName));
    }

    /**
     * Scope a query to only include permissions related to a model.
     *
     * @param Builder $query
     * @param string $model
     * @return Builder
     */
    public function scopeOfModel(Builder $query, $model)
    {
        return $query->where('permissionable_type', $model);
    }

    /**
     * Get the permissionable module type of current permission
     *
     * @return string
     */
    public function getPermissionableModuleAttribute()
    {
        return getModuleNameByModel($this->permissionable_type);
    }

}
