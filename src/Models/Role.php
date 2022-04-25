<?php

namespace A17\Twill\Models;

use A17\Twill\Models\User;
use A17\Twill\Models\Behaviors\HasMedias;
use A17\Twill\Models\Behaviors\HasPermissions;
use A17\Twill\Models\Behaviors\IsTranslatable;
use A17\Twill\Models\Behaviors\HasPosition;
use A17\Twill\Models\Behaviors\Sortable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Role model
 *
 * @property-read string $createdAt Date of creation
 * @property-read string $usersCount Formatted number of users in this role (ie. '123 users')
 * @method static Builder published() Get published roles
 * @method static Builder draft() Get draft roles
 * @method static Builder onlyTrashed() Get trashed roles
 */
class Role extends BaseModel implements Sortable
{
    use HasMedias;
    use SoftDeletes;
    use HasPermissions;
    use IsTranslatable;
    use HasPosition;
    /**
     * @var bool
     */
    public $timestamps = true;

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'published',
        'in_everyone_group',
        'position',
    ];

    /**
     * @var string[]
     */
    protected $dates = [
        'deleted_at',
    ];

    public array $checkboxes = ['published'];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'in_everyone_group' => 'boolean',
    ];

    /**
     * Scope accessible roles for the current user.
     */
    public function scopeAccessible(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder
    {
        $currentUser = auth('twill_users')->user();

        if ($currentUser->isSuperAdmin()) {
            return $query;
        }

        $accessibleRoleIds = $currentUser->role->accessibleRoles->pluck('id')->toArray();

        return $query->whereIn('id', $accessibleRoleIds);
    }

    /**
     * Scope published roles.
     */
    public function scopePublished(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->wherePublished(true);
    }

    /**
     * Scope unpublished (draft) roles.
     */
    public function scopeDraft(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->wherePublished(false);
    }

    /**
     * Scope trashed roles.
     */
    public function scopeOnlyTrashed(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->whereNotNull('deleted_at');
    }

    /**
     * User model relationship
     *
     * @return BelongsToMany|Collection|User[]
     */
    public function users(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Return the formatted created date
     */
    public function getCreatedAtAttribute($value): string
    {
        return \Carbon\Carbon::parse($value)->format('d M Y');
    }

    /**
     * Return the formatted number of users in this group
     */
    public function getUsersCountAttribute($value): string
    {
        return $this->users->count() . ' users';
    }

    /**
     * Return the roles that are considered accessible for this role
     */
    public function getAccessibleRolesAttribute(): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('position', '>=', $this->position)->get();
    }
}
