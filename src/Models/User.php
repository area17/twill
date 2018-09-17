<?php

namespace A17\Twill\Models;

use A17\Twill\Models\Behaviors\HasMedias;
use A17\Twill\Models\Behaviors\HasPresenter;
use A17\Twill\Models\Enums\UserRole;
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
        if (!empty($this->role)) {
            if ($this->role == 'SUPERADMIN') {
                return "SUPERADMIN";
            }

            return UserRole::{$this->role}()->getValue();
        }

        return null;
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
}
