<?php

namespace A17\Twill\Models;

use A17\Twill\Models\Behaviors\HasPermissions;
use A17\Twill\Models\Behaviors\IsTranslatable;
use A17\Twill\Models\Contracts\TwillModelContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class Group extends BaseModel implements TwillModelContract
{
    use HasPermissions;
    use SoftDeletes;
    use IsTranslatable;

    public $timestamps = true;

    protected $fillable = [
        'name',
        'description',
        'published',
        'subdomains_access',
    ];

    public $checkboxes = ['published'];

    protected $casts = [
        'subdomains_access' => 'array',
        'deleted_at' => 'datetime'
    ];

    /**
     * Return the Everyone group.
     */
    public static function getEveryoneGroup(): Group
    {
        return self::where([['is_everyone_group', true], ['name', 'Everyone']])->firstOrFail();
    }

    /**
     * Return the group title.
     */
    public function getTitleInBrowserAttribute(): string
    {
        return $this->name;
    }

    /**
     * Scope published groups.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->wherePublished(true);
    }

    /**
     * Scope unpublished (draft) groups.
     */
    public function scopeDraft(Builder $query): Builder
    {
        return $query->wherePublished(false);
    }

    /**
     * Scope trashed groups.
     */
    public function scopeOnlyTrashed(Builder $query): Builder
    {
        return $query->onlyTrashed();
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(twillModel('user'), 'group_twill_user', 'group_id', 'twill_user_id');
    }

    /**
     * Check if current group is the Everyone group.
     */
    public function isEveryoneGroup(): bool
    {
        return $this->id === self::getEveryoneGroup()->id;
    }

    /**
     * Return the formatted created date.
     */
    public function getCreatedAtAttribute($value): string
    {
        return \Carbon\Carbon::parse($value)->format('d M Y');
    }

    /**
     * Check if the group can be edited (not a system group, ie. Everyone).
     */
    public function getCanEditAttribute(): bool
    {
        return !$this->isEveryoneGroup();
    }

    /**
     * Check if the group can be published (not a system group, ie. Everyone).
     */
    public function getCanPublishAttribute(): bool
    {
        return !$this->isEveryoneGroup();
    }

    /**
     * Return the formatted number of users in this group.
     */
    public function getUsersCountAttribute(): string
    {
        return $this->users->count() . ' users';
    }

    /**
     * Return viewable items.
     */
    public function viewableItems(): Collection
    {
        return Permission::where('name', 'view-item')->whereHas('groups', function ($query) {
            $query->where('id', $this->id);
        })->with('permissionable')->get()->pluck('permissionable');
    }

    /**
     * Return ids of permissionable items.
     *
     * @return int[]
     */
    public function permissionableIds(): array
    {
        return $this->permissions->pluck('id')->toArray();
    }

    /**
     * Return permissionable items.
     */
    public function permissionableItems(): Collection
    {
        return Permission::whereHas('groups', function ($query) {
            $query->where('id', $this->id);
        })->with('permissionable')->get()->pluck('permissionable');
    }

    /**
     * @todo: This originally was not implemented, so I assume we can just pass this on.
     * Perhaps we can add a check here `can:edit-user-groups`?
     */
    public function scopeAccessible(Builder $query): Builder
    {
        return $query;
    }

    public function getTranslatedAttributes(): array
    {
        return [];
    }
}
