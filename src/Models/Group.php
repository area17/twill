<?php

namespace A17\Twill\Models;

use A17\Twill\Models\Behaviors\HasPermissions;
use Illuminate\Database\Eloquent\Model as BaseModel;
use A17\Twill\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends BaseModel
{
    use HasPermissions, SoftDeletes;

    public $timestamps = true;

    protected $fillable = [
        'name',
        'description',
        'published',
        'can_delete',
    ];

    protected $dates = [
        'deleted_at',
    ];

    public $checkboxes = ['published'];

    public static function getEveryoneGroup()
    {
        return Group::where('name', 'Everyone')->firstOrFail();
    }

    public function getTitleInBrowserAttribute()
    {
        return $this->name;
    }

    public function scopePublished($query)
    {
        return $query->wherePublished(true);
    }

    public function scopeDraft($query)
    {
        return $query->wherePublished(false);
    }

    public function scopeOnlyTrashed($query)
    {
        return $query->whereNotNull('deleted_at');
    }

    public function users()
    {
        return $this->belongsToMany('A17\Twill\Models\User', 'group_twill_user', 'group_id', 'twill_user_id');
    }

    public function getCanEditAttribute()
    {
        if ($this->name === "Everyone" && !$this->canDelete) {
            return false;
        }
        return true;
    }

    public function getCanPublishAttribute()
    {
        if ($this->name === "Everyone" && !$this->canDelete) {
            return false;
        }
        return true;
    }

    public function viewableItems()
    {
        return Permission::where('name', 'view-item')->whereHas('groups', function ($query) {
            $query->where('id', $this->id);
        })->with('permissionable')->get()->pluck('permissionable');
    }

}
