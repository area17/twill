<?php

namespace A17\Twill\Models;

use A17\Twill\Models\Behaviors\HasPresenter;
use A17\Twill\Models\Permission;
use Auth;
use Carbon\Carbon;
use Cartalyst\Tags\TaggableInterface;
use Cartalyst\Tags\TaggableTrait;
use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

abstract class Model extends BaseModel implements TaggableInterface
{
    use HasPresenter, SoftDeletes, TaggableTrait;

    public $timestamps = true;

    public function scopePublished($query)
    {
        return $query->wherePublished(true);
    }

    protected static function boot()
    {
        parent::boot();

    }

    public function scopeAccessible($query)
    {
        $model = get_class($query->getModel());
        $moduleName = isPermissionableModule(getModuleNameByModel($model));
        if ( $moduleName && !Auth::user()->is_superadmin) {
            // Get all permissions the logged in user has regards to the model.
            $allPermissions = Auth::user()->allPermissions();
            $allModelPermissions = (clone $allPermissions)->ofModel($model);
            // If the user has any module permissions, or global manage all modules permissions, all items will be return
            if ($allModelPermissions->module()->whereIn('name', Permission::available('module'))->exists()
                || (clone $allPermissions)->global()->where('name', 'manage-modules')->exists()) {
                return $query;
            }

            // If the module is submodule, skip the scope.
            if(strpos($moduleName, '.')) {
                return $query;
            };

            $authorizedItemsIds = $allPermissions->moduleItem()->pluck('permissionable_id');
            return $query->whereIn('id', $authorizedItemsIds);
        }
        return $query;
    }

    public function scopePublishedInListings($query)
    {
        if ($this->isFillable('public')) {
            $query->wherePublic(true);
        }

        return $query->published()->visible();
    }

    public function scopeVisible($query)
    {
        if ($this->isFillable('publish_start_date')) {
            $query->where(function ($query) {
                $query->whereNull('publish_start_date')->orWhere('publish_start_date', '<=', Carbon::now());
            });

            if ($this->isFillable('publish_end_date')) {
                $query->where(function ($query) {
                    $query->whereNull('publish_end_date')->orWhere('publish_end_date', '>=', Carbon::now());
                });
            }
        }

        return $query;
    }

    public function setPublishStartDateAttribute($value)
    {
        $this->attributes['publish_start_date'] = $value ?? Carbon::now();
    }

    public function scopeDraft($query)
    {
        return $query->wherePublished(false);
    }

    public function scopeOnlyTrashed($query)
    {
        return $query->whereNotNull('deleted_at');
    }
}
