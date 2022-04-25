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
 * @property-read string $permissionableModule
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

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'permissionable_type',
        'permissionable_id',
        'is_default'
    ];

    /**
     * @var string[]
     */
    protected $appends = ['permissionable_module'];

    /**
     * Return an array of permission names that belongs to
     * a certain scope (global, module or item).
     *
     * @return string[]|void
     */
    public static function available(string $scope)
    {
        switch ($scope) {
            case Permission::SCOPE_GLOBAL:
                return [
                    'edit-settings',
                    'edit-users',
                    'edit-user-roles',
                    'edit-user-groups',
                    'manage-modules',
                    'access-media-library',
                    'edit-media-library'
                ];
            case Permission::SCOPE_MODULE:
                return array_merge(
                    [
                        'view-module',
                        'edit-module'
                    ],
                    (config('twill.permissions.level') === 'roleGroupItem' ? ['manage-module'] : [])
                );
            case Permission::SCOPE_ITEM:
                return [
                    'view-item',
                    'edit-item',
                    'manage-item'
                ];
        }
    }

    /**
     * Retrieve the list of modules that permissions can be applied to.
     *
     * @return Collection
     */
    public static function permissionableModules(): \Illuminate\Support\Collection
    {
        return collect(config('twill.permissions.modules', []));
    }

    /**
     * Retrieve a collection of items that belongs to keyed by permissionable module names.
     */
    public static function permissionableParentModuleItems(): \Collection
    {
        return self::permissionableModules()->filter(function ($module): bool {
            return !strpos($module, '.');
        })->mapWithKeys(function ($module): array {
            return [$module => getRepositoryByModuleName($module)->get([], [], [], -1)];
        });
    }

    /**
     * Get the parent permissionable model (one of the permissionale module).
     *
     * @return MorphTo|Collection|User[]
     */
    public function permissionable(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }

    /**
     * User model relationship
     *
     * @return BelongsToMany|Collection|User[]
     */
    public function users(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(twillModel('user'), 'permission_twill_user', 'permission_id', 'twill_user_id');
    }

    /**
     * Role model relationship
     *
     * @return BelongsToMany|Collection|BaseModel[]
     */
    public function roles(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(twillModel('role'), 'permission_role', 'permission_id', 'role_id');
    }

    /**
     * Group model relationship
     *
     * @return BelongsToMany|Collection|BaseModel[]
     */
    public function groups(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(twillModel('group'), 'group_permission', 'permission_id', 'group_id');
    }

    /**
     * Scope a query to only include global scope permissions.
     */
    public function scopeGlobal(Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->whereNull('permissionable_type')->whereNull('permissionable_id');
    }

    /**
     * Scope a query to only include module scope permissions.
     */
    public function scopeModule(Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->whereNotNull('permissionable_type')->whereNull('permissionable_id');
    }

    /**
     * Scope a query to only include module item scope permissions.
     */
    public function scopeModuleItem(Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->whereNotNull('permissionable_type')->whereNotNull('permissionable_id');
    }

    /**
     * Scope a query to only include permissions related to an item.
     */
    public function scopeOfItem(Builder $query, BaseModel $item): \Illuminate\Database\Eloquent\Builder
    {
        $permissionableSubmodule = self::permissionableModules()->filter(function($module) use ($item): bool{
            return strpos($module, '.') && explode('.', $module)[1] === getModuleNameByModel($item);
        })->first();

        if ($permissionableSubmodule) {
            $parentRelation = isset($item->parentRelation) ? $item->parentRelation : Str::singular(explode('.', $permissionableSubmodule)[0]);
            $item = $item->$parentRelation;
        }

        return $query->where([
            ['permissionable_type', $item::class],
            ['permissionable_id', $item->id],
        ]);
    }

    /**
     * Scope a query to only include permissions related to a Twill module.
     */
    public function scopeOfModuleName(Builder $query, string $moduleName): \Illuminate\Database\Eloquent\Builder
    {
        // Submodule's permission will inherit from parent module
        if (strpos($moduleName, '.')) {
            $moduleName = explode('.', $moduleName)[0];
        }

        return $query->ofModel(getModelByModuleName($moduleName));
    }

    /**
     * Scope a query to only include permissions related to a model.
     */
    public function scopeOfModel(Builder $query, string $model): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('permissionable_type', $model);
    }

    /**
     * Get the permissionable module type of current permission
     */
    public function getPermissionableModuleAttribute(): string
    {
        return getModuleNameByModel($this->permissionable_type);
    }

}
