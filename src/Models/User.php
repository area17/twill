<?php

namespace A17\Twill\Models;

use A17\Twill\Models\Behaviors\HasMedias;
use A17\Twill\Models\Behaviors\HasOauth;
use A17\Twill\Models\Behaviors\HasPermissions;
use A17\Twill\Models\Behaviors\HasPresenter;
use A17\Twill\Models\Behaviors\IsTranslatable;
use A17\Twill\Models\Group;
use A17\Twill\Models\Role;
use A17\Twill\Notifications\Reset as ResetNotification;
use A17\Twill\Notifications\TemporaryPassword as TemporaryPasswordNotification;
use A17\Twill\Notifications\PasswordResetByAdmin as PasswordResetByAdminNotification;
use A17\Twill\Notifications\Welcome as WelcomeNotification;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Foundation\Auth\User as AuthenticatableContract;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Session;
use PragmaRX\Google2FAQRCode\Google2FA;

class User extends AuthenticatableContract
{

    use Authenticatable, Authorizable, HasMedias, Notifiable, HasPresenter, HasOauth, HasPermissions, SoftDeletes, IsTranslatable;

    public $timestamps = true;

    protected $casts = [
        'is_superadmin' => 'boolean',
        'published' => 'boolean',
    ];

    protected $fillable = [
        'email',
        'name',
        'published',
        'title',
        'description',
        'role_id',
        'google_2fa_enabled',
        'google_2fa_secret',
        'language',
    ];

    protected $dates = [
        'deleted_at',
        'registered_at',
        'last_login_at',
    ];

    protected $hidden = ['password', 'remember_token', 'google_2fa_secret'];
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
        if ($this->is_superadmin) {
            return 'SUPERADMIN';
        }
        return $this->role ? $this->role->name : '';
    }

    public function getCanDeleteAttribute()
    {
        return auth('twill_users')->user()->id !== $this->id;
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

    public function notifyWithCustomMarkdownTheme($instance)
    {
        $hostAppMailConfig = config('mail.markdown.paths');

        config([
            'mail.markdown.paths' => array_merge(
                [__DIR__ . '/../../views/emails'],
                $hostAppMailConfig
            ),
        ]);

        $this->notify($instance);

        config([
            'mail.markdown.paths' => $hostAppMailConfig,
        ]);

    }

    public function sendWelcomeNotification($token)
    {
        $this->notifyWithCustomMarkdownTheme(new WelcomeNotification($token));
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notifyWithCustomMarkdownTheme(new ResetNotification($token));
    }

    public function isSuperAdmin()
    {
        return $this->role === 'SUPERADMIN';
    }

    public function isPublished()
    {
        return (bool) $this->published;
    }

    public function sendTemporaryPasswordNotification($password)
    {
        $this->notify(new TemporaryPasswordNotification($password));
    }

    public function sendPasswordResetByAdminNotification($password)
    {
        $this->notify(new PasswordResetByAdminNotification($password));
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_twill_user', 'twill_user_id', 'group_id');
    }

    public function publishedGroups()
    {
        return $this->groups()->published();
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function allPermissions()
    {
        $permissions = Permission::whereHas('users', function ($query) {
            $query->where('id', $this->id);
        })->orWhereHas('roles', function ($query) {
            $query->where('id', $this->role->id);
        })->orWhereHas('groups', function ($query) {
            $query
                ->join('group_twill_user', 'groups.id', '=', 'group_twill_user.group_id')
                ->where('group_twill_user.twill_user_id', $this->id)
                ->where('published', 1)
            ;
        });
        return $permissions;
    }

    public function getLastLoginColumnValueAttribute()
    {
        return $this->last_login_at ? $this->last_login_at->format('d M Y, H:i') : 'Pending activation';
    }

    public function setGoogle2faSecretAttribute($secret)
    {
        $this->attributes['google_2fa_secret'] = filled($secret) ? \Crypt::encrypt($secret) : null;
    }

    public function getGoogle2faSecretAttribute($secret)
    {
        return filled($secret) ? \Crypt::decrypt($secret) : null;
    }

    public function generate2faSecretKey()
    {
        if (is_null($this->google_2fa_secret)) {
            $secret = (new Google2FA())->generateSecretKey();

            $this->google_2fa_secret = $secret;

            $this->save();
        }
    }

    public function get2faQrCode()
    {
        return (new Google2FA())->getQRCodeInline(
            config('app.name'),
            $this->email,
            $this->google_2fa_secret,
            200
        );
    }
}
