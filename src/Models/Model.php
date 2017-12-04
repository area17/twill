<?php

namespace A17\CmsToolkit\Models;

use A17\CmsToolkit\Models\Behaviors\HasPresenter;
use Auth;
use Carbon\Carbon;
use Cartalyst\Tags\TaggableInterface;
use Cartalyst\Tags\TaggableTrait;
use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

abstract class Model extends BaseModel implements TaggableInterface
{
    use HasPresenter, SoftDeletes, TaggableTrait;

    public $timestamps = true;

    protected static function boot()
    {
        parent::boot();
        static::setTagsModel(Tag::class);
    }

    public function scopePublished($query)
    {
        return $query->wherePublished(true);
    }

    public function scopePublishedInListings($query)
    {
        if ($this->isFillable('public')) {
            $query->wherePublic(true);
        }

        return $query->published()->visible();
    }

    public function scopeVisible($query)
    {
        if ($this->isFillable('publish_start_date')) {
            $query->where(function ($query) {
                $query->whereNull('publish_start_date')->orWhere('publish_start_date', '<=', Carbon::now());
            });
            $query->where(function ($query) {
                $query->whereNull('publish_end_date')->orWhere('publish_end_date', '>=', Carbon::now());
            });
        }

        return $query;
    }

    public function setPublishStartDateAttribute($value)
    {
        $this->attributes['publish_start_date'] = $value ?? Carbon::now();
    }

    public function scopeDraft($query)
    {
        return $query->wherePublished(false);
    }

    public function scopeOnlyTrashed($query)
    {
        return $query->whereNotNull('deleted_at');
    }

    public function isNotLockedByCurrentUser()
    {
        if ($this->isLockable()) {
            if ($this->lockedBy() != null && $this->lockedBy()->id != Auth::user()->id) {
                return true;
            }
        }

        return false;
    }

    public function isLockedByCurrentUser()
    {
        if ($this->isLockable()) {
            if ($this->lockedBy() != null && $this->lockedBy()->id == Auth::user()->id) {
                return true;
            }
        }

        return false;
    }

    public function isLockable()
    {
        if (classHasTrait(get_class($this), 'A17\CmsToolkit\Models\Behaviors\HasLock')) {
            return true;
        }

        return false;
    }

    public function hasRevisions()
    {
        if (classHasTrait(get_class($this), 'A17\CmsToolkit\Models\Behaviors\HasRevisions')) {
            return true;
        }

        return false;
    }
}
