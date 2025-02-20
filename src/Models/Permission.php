<?php

namespace A17\Twill\Models;

use A17\Twill\Enums\PermissionLevel;
use A17\Twill\Exceptions\ModuleNotFoundException;
use A17\Twill\Facades\TwillPermissions;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends BaseModel
{
    /**
     * Constant that represents a list of permissions that belongs to the global scope.
     *
     * @see Permission::available($scope)
     */
    public const SCOPE_GLOBAL = 'global';

    /**
     * Constant that represents a list of permissions that belongs to the module scope.
     *
     * @see Permission::available($scope)
     */
    public const SCOPE_MODULE = 'module';

    /**
     * Constant that represents a list of permissions that belongs to the module item scope.
     *
     * @see Permission::available($scope)
     */
    public const SCOPE_ITEM = 'item';

    protected $fillable = [
        'name',
        'permissionable_type',
        'permissionable_id',
        'is_default',
    ];

    public function __construct(array $attributes = [])
    {
        $this->table = config('twill.permissions_table', 'permissions');
        parent::__construct($attributes);
    }

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
            case self::SCOPE_GLOBAL:
                return [
                    'edit-settings',
                    'edit-users',
                    'edit-user-roles',
                    'edit-user-groups',
                    'manage-modules',
                    'access-media-library',
                    'edit-media-library',
                ];
            case self::SCOPE_MODULE:
                return array_merge(
                    [
                        'view-module',
                        'edit-module',
                    ],
                    (TwillPermissions::levelIs(PermissionLevel::LEVEL_ROLE_GROUP_ITEM) ? ['manage-module'] : [])
                );
            case self::SCOPE_ITEM:
                return [
                    'view-item',
                    'edit-item',
                    'manage-item',
                ];
        }
    }

    /**
     * Retrieve the list of modules that permissions can be applied to.
     *
     * @return Collection
     */
    public static function permissionableModules()
    {
        return collect(config('twill.permissions.modules', []));
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
            try {
                return [$module => getRepositoryByModuleName($module)->get([], [], [], -1)];
            } catch (ModuleNotFoundException $e) {
                return [];
            }
        });
    }

    /**
     * Get the parent permissionable model (one of the permissionale module).
     *
     * @return MorphTo|Collection|User[]
     */
    public function permissionable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * User model relationship
     *
     * @return BelongsToMany|Collection|User[]
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(twillModel('user'), 'permission_twill_user', 'permission_id', 'twill_user_id');
    }

    /**
     * Role model relationship
     *
     * @return BelongsToMany|Collection|BaseModel[]
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(twillModel('role'), 'permission_role', 'permission_id', 'role_id');
    }

    /**
     * Group model relationship
     *
     * @return BelongsToMany|Collection|BaseModel[]
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(twillModel('group'), 'group_permission', 'permission_id', 'group_id');
    }

    /**
     * Scope a query to only include global scope permissions.
     *
     * @return Builder
     */
    public function scopeGlobal(Builder $query)
    {
        return $query->whereNull('permissionable_type')->whereNull('permissionable_id');
    }

    /**
     * Scope a query to only include module scope permissions.
     *
     * @return Builder
     */
    public function scopeModule(Builder $query)
    {
        return $query->whereNotNull('permissionable_type')->whereNull('permissionable_id');
    }

    /**
     * Scope a query to only include module item scope permissions.
     *
     * @return Builder
     */
    public function scopeModuleItem(Builder $query)
    {
        return $query->whereNotNull('permissionable_type')->whereNotNull('permissionable_id');
    }

    /**
     * Scope a query to only include permissions related to an item.
     *
     * @return Builder
     */
    public function scopeOfItem(Builder $query, BaseModel $item)
    {
        $permissionableSubmodule = self::permissionableModules()->filter(function ($module) use ($item) {
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
