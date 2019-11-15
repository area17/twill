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
use Illuminate\Support\Facades\Session;
use PragmaRX\Google2FAQRCode\Google2FA;

class User extends AuthenticatableContract
{
    use Authenticatable, Authorizable, HasMedias, Notifiable, HasPresenter, SoftDeletes;

    public $timestamps = true;

    protected $fillable = [
        'email',
        'name',
        'role',
        'published',
        'title',
        'description',
        'google_2fa_enabled',
        'google_2fa_secret',
    ];

    protected $dates = [
        'deleted_at',
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

    protected $casts = ['published' => 'boolean'];

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
