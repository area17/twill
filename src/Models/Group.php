<?php

namespace A17\Twill\Models;

use A17\Twill\Models\Behaviors\HasMedias;
use A17\Twill\Models\Behaviors\HasPermissions;
use Illuminate\Database\Eloquent\Model as BaseModel;
use A17\Twill\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends BaseModel
{
    use HasMedias, HasPermissions, SoftDeletes;

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

    public $mediasParams = [
        'profile' => [
            'default' => [
                [
                    'name' => 'default',
                    'ratio' => 1,
                ],
            ],
        ],
    ];

    public static function getEveryoneGroup()
    {
        return Group::where('name', 'Everyone')->firstOrFail();
    }

    public function __construct(array $attributes = [])
    {
        $this->table = 'groups';

        parent::__construct($attributes);
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
        if ($this->name === 'Everyone') {
            return User::whereHas('role', function ($query) {
                $query->where('in_everyone_group', true);
            });
        }
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

}
