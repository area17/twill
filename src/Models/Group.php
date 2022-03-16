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

    public $timestamps = true;

    protected $fillable = [
        'name',
        'description',
        'published',
        'subdomains_access',
    ];

    protected $dates = [
        'deleted_at',
    ];

    public $checkboxes = ['published'];

    protected $casts = [
        'subdomains_access' => 'array',
    ];

    /**
     * Return the Everyone group.
     *
     * @return BaseModel
     */
    public static function getEveryoneGroup()
    {
        return Group::where([['is_everyone_group', true], ['name', 'Everyone']])->firstOrFail();
    }

    /**
     * Return the group title.
     *
     * @return string
     */
    public function getTitleInBrowserAttribute()
    {
        return $this->name;
    }

    /**
     * Scope published groups.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopePublished($query)
    {
        return $query->wherePublished(true);
    }

    /**
     * Scope unpublished (draft) groups.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeDraft($query)
    {
        return $query->wherePublished(false);
    }

    /**
     * Scope trashed groups.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeOnlyTrashed($query)
    {
        return $query->whereNotNull('deleted_at');
    }

    /**
     * User model relationship.
     *
     * @return BelongsToMany|Collection|User[]
     */
    public function users()
    {
        return $this->belongsToMany(twillModel('user'), 'group_twill_user', 'group_id', 'twill_user_id');
    }

    /**
     * Check if current group is the Everyone group.
     *
     * @return bool
     */
    public function isEveryoneGroup()
    {
        return $this->id === $this->getEveryoneGroup()->id;
    }

    /**
     * Return the formatted created date.
     *
     * @return string
     */
    public function getCreatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->format('d M Y');
    }

    /**
     * Check if the group can be edited (not a system group, ie. Everyone).
     *
     * @return bool
     */
    public function getCanEditAttribute()
    {
        return ! $this->isEveryoneGroup();
    }

    /**
     * Check if the group can be published (not a system group, ie. Everyone).
     *
     * @return bool
     */
    public function getCanPublishAttribute()
    {
        return ! $this->isEveryoneGroup();
    }

    /**
     * Return the formatted number of users in this group.
     *
     * @return string
     */
    public function getUsersCountAttribute()
    {
        return $this->users->count() . ' users';
    }

    /**
     * Return viewable items.
     *
     * @return Collection
     */
    public function viewableItems()
    {
        /* @phpstan-ignore-next-line */
        return Permission::where('name', 'view-item')->whereHas('groups', function ($query) {
            $query->where('id', $this->id);
        })->with('permissionable')->get()->pluck('permissionable');
    }

    /**
     * Return ids of permissionable items.
     *
     * @return int[]
     */
    public function permissionableIds()
    {
        return $this->permissions->pluck('id')->toArray();
    }

    /**
     * Return permissionable items.
     *
     * @return Collection
     */
    public function permissionableItems()
    {
        return Permission::whereHas('groups', function ($query) {
            $query->where('id', $this->id);
        })->with('permissionable')->get()->pluck('permissionable');
    }
}
