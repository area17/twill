<?php

namespace A17\Twill\Models;

use A17\Twill\Models\Behaviors\HasPresenter;
use A17\Twill\Models\Permission;
use Auth;
use Carbon\Carbon;
use Cartalyst\Tags\TaggableInterface;
use Cartalyst\Tags\TaggableTrait;
use DB;
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
        $permission_models = Permission::permissionableModules()->map(function ($moduleName) {
            return config('twill.namespace') . "\\Models\\" . studly_case(str_singular($moduleName));
        });
        $model = get_class($query->getModel());
        //The current model is an permission-enabled model
        if ($permission_models->contains($model)) {
            $authorizedItemsIds = DB::table('permissions')
                ->rightJoin('permission_twill_user', 'permissions.id', '=', 'permission_id')
                ->where([
                    ['twill_user_id', Auth::user()->id],
                    ['permissionable_type', $model],
                ])
                ->groupBy('permissionable_id')
                ->select('permissionable_id')
                ->get()
                ->pluck('permissionable_id');
            // $user_permissions = Permission::where('permissionable_type', $model)->whereHas('users', function ($query) {
            //     $query->where('id', Auth::user()->id);
            // });
            // $role_permissions = Permission::where('permissionable_type', $model)->whereHas('roles', function ($query) {
            //     $query->where('id', Auth::user()->role);
            // });
            //get all records of this model that user could access
            // $authorizedItemsIds = $builder->withoutGlobalScope('accessible')->get()->filter(function ($item) {
            //     return Auth::user()->can('view-item', $item);
            // })->pluck('id');
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
