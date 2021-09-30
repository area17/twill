<?php

namespace A17\Twill\Models\Behaviors;

use A17\Twill\Models\Behaviors\HasPresenter;
use A17\Twill\Services\Capsules\HasCapsules;
use A17\Twill\Models\Behaviors\IsTranslatable;
use A17\Twill\Models\Tag;
use Carbon\Carbon;
use Cartalyst\Tags\TaggableTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

trait TwillModel
{
    use SoftDeletes;
    use HasPresenter;
    use SoftDeletes;
    use TaggableTrait;
    use IsTranslatable;
    use HasCapsules;

    /**
     * @return bool
     */
    protected function isTranslationModel(): bool
    {
        return Str::endsWith(get_class($this), 'Translation');
    }

    /** @inheritDoc */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where("{$this->getTable()}.published", true);
    }

    /** @inheritDoc */
    public function scopePublishedInListings(Builder $query): Builder
    {
        if ($this->isFillable('public')) {
            $query->where("{$this->getTable()}.public", true);
        }

        return $query->published()->visible();
    }

    /** @inheritDoc */
    public function scopeVisible(Builder $query): Builder
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

    /** @inheritDoc */
    public function setPublishStartDateAttribute($value): void
    {
        $this->attributes['publish_start_date'] = $value ?? Carbon::now();
    }

    /** @inheritDoc */
    public function scopeDraft(Builder $query): Builder
    {
        return $query->where("{$this->getTable()}.published", false);
    }

    /** @inheritDoc */
    public function scopeOnlyTrashed(Builder $query): Builder
    {
        return $query->whereNotNull("{$this->getTable()}.deleted_at");
    }

    /** @inheritDoc */
    public function getFillable(): array
    {
        // If the fillable attribute is filled, just use it
        $fillable = $this->fillable;

        // If fillable is empty
        // and it's a translation model
        // and the baseModel was defined
        // Use the list of translatable attributes on our base model
        if (blank($fillable) && $this->isTranslationModel() && property_exists($this, 'baseModuleModel')) {
            $baseModelClass = $this->baseModuleModel;
            $fillable = (new $baseModelClass())->getTranslatedAttributes();

            if (!collect($fillable)->contains('locale')) {
                $fillable[] = 'locale';
            }

            if (!collect($fillable)->contains('active')) {
                $fillable[] = 'active';
            }
        }

        return $fillable;
    }

    /** @inheritDoc */
    public function getTranslatedAttributes(): array
    {
        return $this->translatedAttributes ?? [];
    }

    /** @inheritDoc */
    protected static function bootTaggableTrait(): void
    {
        static::$tagsModel = Tag::class;
    }

    /** @inheritDoc */
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
