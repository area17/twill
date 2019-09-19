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
    ];

    protected $dates = [
        'deleted_at',
    ];

    public $checkboxes = ['published'];

    public static function getEveryoneGroup()
    {
        return Group::where([['is_everyone_group', true], ['name', 'Everyone']])->firstOrFail();
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
        return $this->belongsToMany(User::class, 'group_twill_user', 'group_id', 'twill_user_id');
    }

    public function isEveryoneGroup()
    {
        return $this->id === $this->getEveryoneGroup()->id;
    }

    public function getCreatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->format('d M Y');
    }

    public function getCanEditAttribute()
    {
        return !$this->isEveryoneGroup();
    }

    public function getCanPublishAttribute()
    {
        return !$this->isEveryoneGroup();
    }

    public function getUsersCountAttribute($value)
    {
        return $this->users->count() . ' users';
    }

    public function viewableItems()
    {
        return Permission::where('name', 'view-item')->whereHas('groups', function ($query) {
            $query->where('id', $this->id);
        })->with('permissionable')->get()->pluck('permissionable');
    }

}
