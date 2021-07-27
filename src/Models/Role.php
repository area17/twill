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
    use HasMedias, SoftDeletes, HasPermissions, IsTranslatable, HasPosition;

    public $timestamps = true;

    protected $fillable = [
        'name',
        'published',
        'in_everyone_group',
        'position',
    ];

    protected $dates = [
        'deleted_at',
    ];

    public $checkboxes = ['published'];

    protected $casts = [
        'in_everyone_group' => 'boolean',
    ];

    /**
     * Scope accessible roles for the current user.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeAccessible($query)
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
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopePublished($query)
    {
        return $query->wherePublished(true);
    }

    /**
     * Scope unpublished (draft) roles.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeDraft($query)
    {
        return $query->wherePublished(false);
    }

    /**
     * Scope trashed roles.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeOnlyTrashed($query)
    {
        return $query->whereNotNull('deleted_at');
    }

    /**
     * User model relationship
     *
     * @return BelongsToMany|Collection|User[]
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Return the formatted created date
     *
     * @return string
     */
    public function getCreatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->format('d M Y');
    }

    /**
     * Return the formatted number of users in this group
     *
     * @return string
     */
    public function getUsersCountAttribute($value)
    {
        return $this->users->count() . ' users';
    }

    /**
     * Return the roles that are considered accessible for this role
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAccessibleRolesAttribute()
    {
        return static::where('position', '>=', $this->position)->get();
    }
}
