<?php

namespace A17\Twill\Models;

use A17\Twill\Models\Behaviors\HasPermissions;
use A17\Twill\Models\Behaviors\IsTranslatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Group model.
 *
 * @property-read string $titleInBrowser Title
 * @property-read string $createdAt Date of creation
 * @property-read string $name Name
 * @property-read bool $canEdit Check if the group is editable (ie. not the Everyone group)
 * @property-read bool $canPublish Check if the group is publishable (ie. not the Everyone group)
 * @property-read string $usersCount Formatted number of users in this group (ie. '123 users')
 * @method static Builder published() Get published groups
 * @method static Builder draft() Get draft groups
 * @method static Builder onlyTrashed() Get trashed groups
 */
class Group extends BaseModel
{
    use HasPermissions;
    use SoftDeletes;
    use IsTranslatable;

    /**
     * @var bool
     */
    public $timestamps = true;

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'description',
        'published',
        'subdomains_access',
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
        'subdomains_access' => 'array',
    ];

    /**
     * Return the Everyone group.
     *
     * @return BaseModel
     */
    public static function getEveryoneGroup(): BaseModel
    {
        return Group::where([['is_everyone_group', true], ['name', 'Everyone']])->firstOrFail();
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
    public function scopePublished(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->wherePublished(true);
    }

    /**
     * Scope unpublished (draft) groups.
     */
    public function scopeDraft(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->wherePublished(false);
    }

    /**
     * Scope trashed groups.
     */
    public function scopeOnlyTrashed(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->whereNotNull('deleted_at');
    }

    /**
     * User model relationship.
     *
     * @return BelongsToMany|Collection|User[]
     */
    public function users(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(twillModel('user'), 'group_twill_user', 'group_id', 'twill_user_id');
    }

    /**
     * Check if current group is the Everyone group.
     */
    public function isEveryoneGroup(): bool
    {
        return $this->id === $this->getEveryoneGroup()->id;
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
        return ! $this->isEveryoneGroup();
    }

    /**
     * Check if the group can be published (not a system group, ie. Everyone).
     */
    public function getCanPublishAttribute(): bool
    {
        return ! $this->isEveryoneGroup();
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
    public function viewableItems(): \Collection
    {
        /* @phpstan-ignore-next-line */
        return Permission::where('name', 'view-item')->whereHas('groups', function ($query): void {
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
    public function permissionableItems(): \Collection
    {
        return Permission::whereHas('groups', function ($query): void {
            $query->where('id', $this->id);
        })->with('permissionable')->get()->pluck('permissionable');
    }
}
