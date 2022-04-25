<?php

namespace A17\Twill\Models;

use A17\Twill\Models\Behaviors\HasMedias;
use A17\Twill\Models\Behaviors\HasOauth;
use A17\Twill\Models\Behaviors\HasPermissions;
use A17\Twill\Models\Behaviors\HasPresenter;
use A17\Twill\Models\Behaviors\IsTranslatable;
use A17\Twill\Models\Enums\UserRole;
use A17\Twill\Models\Group;
use A17\Twill\Models\Role;
use A17\Twill\Notifications\PasswordResetByAdmin as PasswordResetByAdminNotification;
use A17\Twill\Notifications\Reset as ResetNotification;
use A17\Twill\Notifications\TemporaryPassword as TemporaryPasswordNotification;
use A17\Twill\Notifications\Welcome as WelcomeNotification;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Foundation\Auth\User as AuthenticatableContract;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use PragmaRX\Google2FAQRCode\Google2FA;

/**
 * @property-read string $name Name
 */
class User extends AuthenticatableContract
{
    use Authenticatable;
    use Authorizable;
    use HasMedias;
    use Notifiable;
    use HasPresenter;
    use HasOauth;
    use HasPermissions;
    use SoftDeletes;
    use IsTranslatable;

    /**
     * @var bool
     */
    public $timestamps = true;

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'is_superadmin' => 'boolean',
        'published' => 'boolean',
    ];

    /**
     * @var string[]
     */
    protected $fillable = [
        'email',
        'name',
        'role',
        'published',
        'title',
        'description',
        'role_id',
        'google_2fa_enabled',
        'google_2fa_secret',
        'language',
    ];

    /**
     * @var string[]
     */
    protected $dates = [
        'deleted_at',
        'registered_at',
        'last_login_at',
    ];

    /**
     * @var string[]
     */
    protected $hidden = ['password', 'remember_token', 'google_2fa_secret'];

    public array $checkboxes = ['published'];

    public array $mediasParams = [
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

    /**
     * Scope accessible users for the current user.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeAccessible($query)
    {
        /** @var self $currentUser */
        $currentUser = auth('twill_users')->user();

        if (! config('twill.enabled.permissions-management') || $currentUser->isSuperAdmin()) {
            return $query;
        }

        $accessibleRoleIds = $currentUser->role->accessibleRoles->pluck('id')->toArray();

        return $query->whereIn('role_id', $accessibleRoleIds);
    }

    public static function getRoleColumnName(): string
    {
        if (config('twill.enabled.permissions-management')) {
            return 'role_id';
        }

        return 'role';
    }

    public function getTitleInBrowserAttribute(): string
    {
        return $this->name;
    }

    public function getRoleValueAttribute()
    {
        if ($this->is_superadmin || $this->role == 'SUPERADMIN') {
            return 'SUPERADMIN';
        }

        if (config('twill.enabled.permissions-management')) {
            return $this->role ? $this->role->name : null;
        }

        if (! empty($this->role)) {
            return UserRole::{$this->role}()->getValue();
        }

        return null;
    }

    public function getCanDeleteAttribute(): bool
    {
        return auth('twill_users')->user()->id !== $this->id;
    }

    public function scopeActivated($query)
    {
        return $query->whereNotNull('registered_at')->published();
    }

    public function scopePending($query)
    {
        return $query->whereNull('registered_at')->published();
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

    public function scopeNotSuperAdmin($query)
    {
        if (config('twill.enabled.permissions-management')) {
            return $query->where('is_superadmin', '<>', true);
        }

        return $query->where('role', '<>', 'SUPERADMIN');
    }

    public function setImpersonating($id): void
    {
        Session::put('impersonate', $id);
    }

    public function stopImpersonating(): void
    {
        Session::forget('impersonate');
    }

    public function isImpersonating(): bool
    {
        return Session::has('impersonate');
    }

    public function notifyWithCustomMarkdownTheme($instance): void
    {
        $hostAppMailConfig = config('mail.markdown.paths') ?? [];

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

    public function sendWelcomeNotification($token): void
    {
        $this->notifyWithCustomMarkdownTheme(new WelcomeNotification($token));
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notifyWithCustomMarkdownTheme(new ResetNotification($token));
    }

    public function isSuperAdmin()
    {
        if (config('twill.enabled.permissions-management')) {
            return $this->is_superadmin;
        }

        return $this->role === 'SUPERADMIN';
    }

    public function isPublished(): bool
    {
        return (bool) $this->published;
    }

    public function isActivated(): bool
    {
        return (bool) $this->registered_at;
    }

    public function sendTemporaryPasswordNotification($password): void
    {
        $this->notify(new TemporaryPasswordNotification($password));
    }

    public function sendPasswordResetByAdminNotification($password): void
    {
        $this->notify(new PasswordResetByAdminNotification($password));
    }

    public function groups(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'group_twill_user', 'twill_user_id', 'group_id');
    }

    public function publishedGroups()
    {
        return $this->groups()->published();
    }

    public function role(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function allPermissions()
    {
        return Permission::whereHas('users', function ($query): void {
            $query->where('id', $this->id);
        })->orWhereHas('roles', function ($query): void {
            $query->where('id', $this->role->id);
        })->orWhereHas('groups', function ($query): void {
            $query
                ->join('group_twill_user', 'groups.id', '=', 'group_twill_user.group_id')
                ->where('group_twill_user.twill_user_id', $this->id)
                ->where('published', 1);
        });
    }

    public function getLastLoginColumnValueAttribute()
    {
        return $this->last_login_at ?
            $this->last_login_at->format('d M Y, H:i') :
            ($this->isActivated() ? '&mdash;' : twillTrans('twill::lang.user-management.activation-pending'));
    }

    public function setGoogle2faSecretAttribute($secret): void
    {
        $this->attributes['google_2fa_secret'] = filled($secret) ? Crypt::encrypt($secret) : null;
    }

    public function getGoogle2faSecretAttribute($secret)
    {
        return filled($secret) ? Crypt::decrypt($secret) : null;
    }

    public function generate2faSecretKey(): void
    {
        if (is_null($this->google_2fa_secret)) {
            $secret = (new Google2FA())->generateSecretKey();

            $this->google_2fa_secret = $secret;

            $this->save();
        }
    }

    public function get2faQrCode(): string
    {
        return (new Google2FA())->getQRCodeInline(
            config('app.name'),
            $this->email,
            $this->google_2fa_secret,
            200
        );
    }
}
