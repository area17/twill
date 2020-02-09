<?php

namespace A17\Twill\Models;

use A17\Twill\Models\Behaviors\HasPresenter;
use Carbon\Carbon;
use Cartalyst\Tags\TaggableInterface;
use Cartalyst\Tags\TaggableTrait;
use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

abstract class Model extends BaseModel implements TaggableInterface
{
    use HasPresenter, SoftDeletes, TaggableTrait;

    public $timestamps = true;

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

            if ($this->isFillable('publish_end_date')) {
                $query->where(function ($query) {
                    $query->whereNull('publish_end_date')->orWhere('publish_end_date', '>=', Carbon::now());
                });
            }
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

    public function isTranslatable($columns = null)
    {
        // Model must have the trait
        if (!classHasTrait($this, 'A17\Twill\Models\Behaviors\HasTranslation')) {
            return false;
        }

        // Model must have the translatedAttributes property
        if (!property_exists($this, 'translatedAttributes')) {
            return false;
        }

        // If it's a check on certain columns
        // They must be present in the translatedAttributes
        if (filled($columns)) {
            return collect($this->translatedAttributes)
                ->intersect(collect($columns))
                ->isNotEmpty();
        }

        // The translatedAttributes property must be filled
        return collect($this->translatedAttributes)->isNotEmpty();
    }
}
