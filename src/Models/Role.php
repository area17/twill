<?php

namespace A17\Twill\Models;

use A17\Twill\Models\Behaviors\HasMedias;
use A17\Twill\Models\Behaviors\HasPermissions;
use A17\Twill\Models\Behaviors\IsTranslatable;
use A17\Twill\Models\Behaviors\HasPosition;
use A17\Twill\Models\Behaviors\Sortable;
use A17\Twill\Models\Contracts\TwillModelContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model as BaseModel;

class Role extends BaseModel implements Sortable, TwillModelContract
{
    use HasMedias;
    use SoftDeletes;
    use HasPermissions;
    use IsTranslatable;
    use HasPosition;

    public $timestamps = true;

    protected $fillable = [
        'name',
        'published',
        'in_everyone_group',
        'position',
    ];

    public $checkboxes = ['published'];

    protected $casts = [
        'in_everyone_group' => 'boolean',
        'deleted_at' => 'datetime'
    ];

    public function __construct(array $attributes = [])
    {
        $this->table = config('twill.roles_table', 'roles');
        parent::__construct($attributes);
    }

    public function scopeAccessible($query): Builder
    {
        $currentUser = auth('twill_users')->user();

        if ($currentUser?->isSuperAdmin()) {
            return $query;
        }

        $accessibleRoleIds = $currentUser?->role->accessibleRoles->pluck('id')->toArray();

        return $query->whereIn('id', $accessibleRoleIds);
    }

    public function scopePublished($query): Builder
    {
        return $query->wherePublished(true);
    }

    public function scopeDraft($query): Builder
    {
        return $query->wherePublished(false);
    }

    public function scopeOnlyTrashed($query): Builder
    {
        return $query->onlyTrashed();
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

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
    public function getAccessibleRolesAttribute(): Collection
    {
        return static::where('position', '>=', $this->position)->get();
    }

    public function getTranslatedAttributes(): array
    {
        return [];
    }
}
