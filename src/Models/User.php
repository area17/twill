<?php

namespace A17\Twill\Models;

use A17\Twill\Models\Behaviors\HasMedias;
use A17\Twill\Models\Behaviors\HasPresenter;
use A17\Twill\Models\Group;
use A17\Twill\Notifications\Reset as ResetNotification;
use A17\Twill\Notifications\Welcome as WelcomeNotification;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Foundation\Auth\User as AuthenticatableContract;
use Illuminate\Notifications\Notifiable;
use Session;

class User extends AuthenticatableContract
{
    use Authenticatable, Authorizable, HasMedias, Notifiable, HasPresenter, SoftDeletes;

    public $timestamps = true;

    protected $fillable = [
        'email',
        'name',
        'role',
        'published',
        'password',
        'title',
        'description',
        'role_id',
    ];

    protected $dates = [
        'deleted_at',
    ];

    protected $hidden = ['password', 'remember_token'];
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

    public static function boot()
    {
        parent::boot();

        // Once a new user is created, add it to Everyone group
        self::created(function ($user) {
            if (!$user->is_superadmin && $user->role->in_everyone_group) {
                $everyoneGroup = Group::where([['name', 'Everyone'], ['can_delete', false]])->first();
                $everyoneGroup->users()->attach($user->id);
            }
        });
    }

    public function __construct(array $attributes = [])
    {
        $this->table = config('twill.users_table', 'twill_users');

        parent::__construct($attributes);
    }

    public function getTitleInBrowserAttribute()
    {
        return $this->name;
    }

    public function getRoleValueAttribute()
    {
        if ($this->is_superadmin) {
            return 'SUPERADMIN';
        }
        return $this->role ? $this->role->name : '';
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

    public function setImpersonating($id)
    {
        Session::put('impersonate', $id);
    }

    public function stopImpersonating()
    {
        Session::forget('impersonate');
    }

    public function isImpersonating()
    {
        return Session::has('impersonate');
    }

    public function sendWelcomeNotification($token)
    {
        $this->notify(new WelcomeNotification($token));
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetNotification($token));
    }

    public function permissions()
    {
        return $this->belongsToMany('A17\Twill\Models\Permission', 'permission_twill_user', 'twill_user_id', 'permission_id');
    }

    public function groups()
    {
        return $this->belongsToMany('A17\Twill\Models\Group', 'group_twill_user', 'twill_user_id', 'group_id');
    }

    public function role()
    {
        return $this->belongsTo('A17\Twill\Models\Role');
    }

    public function itemPermission($item)
    {
        return $this->permissions()->where([
            ['permissionable_type', get_class($item)],
            ['permissionable_id', $item->id],
        ])->first();
    }

    public function itemPermissionName($item)
    {
        return $this->itemPermission($item) ? $this->itemPermission($item)->name : null;
    }

    protected $casts = [
        'is_superadmin' => 'boolean',
    ];
}
