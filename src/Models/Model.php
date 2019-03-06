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
        $permissionModels = Permission::permissionableModules()->map(function ($moduleName) {
            return config('twill.namespace') . "\\Models\\" . studly_case(str_singular($moduleName));
        });
        $model = get_class($query->getModel());
        //The current model is an permission-enabled model
        if ($permissionModels->contains($model)) {
            $allPermissions = Auth::user()->userAllPermissions()->where('permissionable_type', $model);
            // If the user has any module permissions, or global manage all modules permissions, all items will be return
            if ($allPermissions->whereNull('permissionable_id')->whereIn('name', Permission::available('module'))->exists()) {
                return $query;
            }

            $authorizedItemsIds = $allPermissions->where('');
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
