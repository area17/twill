<?php

namespace A17\Twill\Models;

use A17\Twill\Models\Behaviors\HasDates;
use A17\Twill\Models\Behaviors\HasPresenter;
use A17\Twill\Models\Behaviors\IsTranslatable;
use Carbon\Carbon;
use Cartalyst\Tags\TaggableInterface;
use Cartalyst\Tags\TaggableTrait;
use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

abstract class Model extends BaseModel implements TaggableInterface
{
    use HasPresenter, SoftDeletes, TaggableTrait, IsTranslatable, HasDates;

    public $timestamps = true;

    protected function isTranslationModel()
    {
        return Str::endsWith(get_class($this), 'Translation');
    }

    public function scopePublished($query)
    {
        return $query->where("{$this->getTable()}.published", true);
    }

    public function scopePublishedInListings($query)
    {
        if ($this->isFillable('public')) {
            $query->where("{$this->getTable()}.public", true);

        }

        return $query->published()->visible();
    }

    public function scopeVisible($query)
    {
        if ($this->isFillable('publish_start_date')) {
            $query->where(function ($query) {
                $query->whereNull("{$this->getTable()}.publish_start_date")->orWhere("{$this->getTable()}.publish_start_date", '<=', Carbon::now());
            });

            if ($this->isFillable('publish_end_date')) {
                $query->where(function ($query) {
                    $query->whereNull("{$this->getTable()}.publish_end_date")->orWhere("{$this->getTable()}.publish_end_date", '>=', Carbon::now());
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
        return $query->where("{$this->getTable()}.published", false);
    }

    public function scopeOnlyTrashed($query)
    {
        return $query->whereNotNull("{$this->getTable()}.deleted_at");
    }

    public function getFillable()
    {
        // If the fillable attribute is filled, just use it
        $fillable = $this->fillable;

        // If fillable is empty
        // and it's a translation model
        // and the baseModel was defined
        // Use the list of translatable attributes on our base model
        if (
            blank($fillable) &&
            $this->isTranslationModel() &&
            property_exists($this, 'baseModuleModel')
        ) {
            $fillable = (new $this->baseModuleModel)->getTranslatedAttributes();

            if (!collect($fillable)->contains('locale')) {
                $fillable[] = 'locale';
            }

            if (!collect($fillable)->contains('active')) {
                $fillable[] = 'active';
            }
        }

        return $fillable;
    }

    public function getTranslatedAttributes()
    {
        return $this->translatedAttributes ?? [];
    }

    protected static function bootTaggableTrait()
    {
        static::$tagsModel = Tag::class;
    }

    /**
     * {@inheritdoc}
     */
    public function tags(): MorphToMany
    {
        return $this->morphToMany(
            static::$tagsModel,
            'taggable',
            config('twill.tagged_table', 'tagged'),
            'taggable_id',
            'tag_id'
        );
    }
}
