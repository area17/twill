<?php

namespace A17\Twill\Models;

use A17\Twill\Models\User;
use A17\Twill\Models\Behaviors\HasMedias;
use A17\Twill\Models\Behaviors\HasPermissions;
use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends BaseModel
{
    use HasMedias, SoftDeletes, HasPermissions;

    public $timestamps = true;

    protected $fillable = [
        'name',
        'published',
        'in_everyone_group',
    ];

    protected $dates = [
        'deleted_at',
    ];

    public $checkboxes = ['published'];

    protected $casts = [
        'in_everyone_group' => 'boolean',
    ];

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
        return $this->hasMany(User::class);
    }

    public function getCreatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->format('d M Y');
    }

    public function getUsersCountAttribute($value)
    {
        return $this->users->count() . ' users';
    }
}
