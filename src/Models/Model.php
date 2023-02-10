<?php

namespace A17\Twill\Models;

use A17\Twill\Facades\TwillPermissions;
use A17\Twill\Models\Behaviors\HasPresenter;
use A17\Twill\Models\Behaviors\IsTranslatable;
use A17\Twill\Models\Contracts\TwillLinkableModel;
use A17\Twill\Models\Contracts\TwillModelContract;
use A17\Twill\Models\Contracts\TwillSchedulableModel;
use Carbon\Carbon;
use Cartalyst\Tags\TaggableInterface;
use Cartalyst\Tags\TaggableTrait;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

abstract class Model extends BaseModel implements TaggableInterface, TwillModelContract, TwillSchedulableModel, TwillLinkableModel
{
    use HasPresenter;
    use SoftDeletes;
    use TaggableTrait;
    use IsTranslatable;

    public $timestamps = true;

    protected function isTranslationModel(): bool
    {
        return Str::endsWith(get_class($this), 'Translation');
    }

    public function scopePublished($query): Builder
    {
        return $query->where("{$this->getTable()}.published", true);
    }

    public function scopeAccessible($query): Builder
    {
        if (! TwillPermissions::enabled()) {
            return $query;
        }

        $model = get_class($query->getModel());
        $moduleName = TwillPermissions::getPermissionModule(getModuleNameByModel($model));

        if ($moduleName && ! Auth::user()->isSuperAdmin()) {
            // Get all permissions the logged in user has regards to the model.
            $allPermissions = Auth::user()->allPermissions();
            $allModelPermissions = (clone $allPermissions)->ofModel($model);

            // If the user has any module permissions, or global manage all modules permissions, all items will be return
            if (
                (clone $allModelPermissions)->module()
                    ->whereIn('name', Permission::available(Permission::SCOPE_MODULE))
                    ->exists()
                || (clone $allPermissions)->global()->where('name', 'manage-modules')->exists()
            ) {
                return $query;
            }

            // If the module is submodule, skip the scope.
            if (strpos($moduleName, '.')) {
                return $query;
            }

            $authorizedItemsIds = $allModelPermissions->moduleItem()->pluck('permissionable_id');

            return $query->whereIn($this->getTable() . '.id', $authorizedItemsIds);
        }

        return $query;
    }

    public function scopePublishedInListings($query): Builder
    {
        // @todo: Remove? Seems unused.
        if ($this->isFillable('public')) {
            $query->where("{$this->getTable()}.public", true);
        }

        return $query->published()->visible();
    }

    /**
     * @todo: Document
     */
    public function scopeVisible($query): Builder
    {
        if ($this->isFillable('publish_start_date')) {
            $query->where(function ($query) {
                $query->whereNull("{$this->getTable()}.publish_start_date")->orWhere(
                    "{$this->getTable()}.publish_start_date",
                    '<=',
                    Carbon::now()
                );
            });

            if ($this->isFillable('publish_end_date')) {
                $query->where(function ($query) {
                    $query->whereNull("{$this->getTable()}.publish_end_date")->orWhere(
                        "{$this->getTable()}.publish_end_date",
                        '>=',
                        Carbon::now()
                    );
                });
            }
        }

        return $query;
    }

    public function setPublishStartDateAttribute($value): void
    {
        $this->attributes['publish_start_date'] = $value ?? Carbon::now();
    }

    public function scopeDraft($query): Builder
    {
        return $query->where("{$this->getTable()}.published", false);
    }

    public function scopeOnlyTrashed($query): Builder
    {
        return $query->onlyTrashed();
    }

    public function getFillable(): array
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
            $fillable = (new $this->baseModuleModel())->getTranslatedAttributes();

            if (! collect($fillable)->contains('locale')) {
                $fillable[] = 'locale';
            }

            if (! collect($fillable)->contains('active')) {
                $fillable[] = 'active';
            }
        }

        return $fillable;
    }

    public function getTranslatedAttributes(): array
    {
        return $this->translatedAttributes ?? [];
    }

    protected static function bootTaggableTrait(): void
    {
        static::$tagsModel = Tag::class;
    }

    /**
     * @inheritdoc
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

    public function getFullUrl(): string
    {
        if (! method_exists($this, 'getSlug')) {
            return '#';
        }

        // @phpstan-ignore-next-line
        if (method_exists($this, 'getUrlWithoutSlug') && $this->urlWithoutSlug) {
            return rtrim($this->urlWithoutSlug, '/') . '/' . $this->getSlug();
        }

        try {
            $controller = getModelController($this);
        } catch (Exception) {
            // Fallback to never crash on production.
            return '#';
        }

        return Str::replace(
            ['/{preview}', '{language}'],
            ['', app()->getLocale()],
            rtrim($controller->getPermalinkBaseUrl(), '/') . '/' . $this->getSlug()
        );
    }
}
