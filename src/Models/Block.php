<?php

namespace A17\Twill\Models;

use A17\Twill\Facades\TwillUtil;
use A17\Twill\Models\Behaviors\HasFiles;
use A17\Twill\Models\Behaviors\HasMedias;
use A17\Twill\Models\Behaviors\HasPresenter;
use A17\Twill\Models\Behaviors\HasRelated;
use A17\Twill\Models\Contracts\TwillModelContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Block extends BaseModel implements TwillModelContract
{
    use HasMedias;
    use HasFiles;
    use HasPresenter;
    use HasRelated;

    public $timestamps = false;

    protected $fillable = [
        'blockable_id',
        'blockable_type',
        'position',
        'content',
        'type',
        'child_key',
        'parent_id',
        'editor_name',
    ];

    protected $casts = [
        'content' => 'array',
    ];

    protected $with = ['medias', 'children'];

    public function scopeEditor($query, $name = 'default')
    {
        return $name === 'default' ?
            $query->where('editor_name', $name)->orWhereNull('editor_name') :
            $query->where('editor_name', $name);
    }

    public function blockable(): MorphTo
    {
        return $this->morphTo();
    }

    public function children(): HasMany
    {
        return $this->hasMany(twillModel('block'), 'parent_id')
            ->orderBy(
                $this->getTable() . '.position',
                'asc'
            );
    }

    public function wysiwyg(string $name): string
    {
        return TwillUtil::parseInternalLinks($this->input($name) ?? '');
    }

    public function translatedWysiwyg(string $name): string
    {
        return TwillUtil::parseInternalLinks($this->translatedInput($name) ?? '');
    }

    public function input(string $name): mixed
    {
        return $this->content[$name] ?? null;
    }

    public function translatedInput(string $name, bool $forceLocale = null): mixed
    {
        $value = $this->content[$name] ?? null;

        $locale = $forceLocale ?? (
        config('translatable.use_property_fallback', false) && (! array_key_exists(
            app()->getLocale(),
            array_filter($value ?? []) ?? []
        ))
            ? config('translatable.fallback_locale')
            : app()->getLocale()
        );

        return $value[$locale] ?? null;
    }

    public function browserIds($name)
    {
        return isset($this->content['browsers']) ? ($this->content['browsers'][$name] ?? []) : [];
    }

    public function checkbox($name)
    {
        return isset($this->content[$name]) && ($this->content[$name][0] ?? $this->content[$name] ?? false);
    }

    public function getPresenterAttribute()
    {
        if ($presenter = config('twill.block_editor.block_presenter_path')) {
            return $presenter;
        }

        return null;
    }

    public function getTable()
    {
        return config('twill.blocks_table', 'twill_blocks');
    }

    public function scopePublished(Builder $query): Builder
    {
        // @todo: These are not used yet by the block editor.
        return $query;
    }

    public function scopeAccessible(Builder $query): Builder
    {
        // @todo: These are not used yet by the block editor.
        return $query;
    }

    public function scopeOnlyTrashed(Builder $query): Builder
    {
        // @todo: These are not used yet by the block editor.
        return $query;
    }

    public function scopeDraft(Builder $query): Builder
    {
        // @todo: These are not used yet by the block editor.
        return $query;
    }

    public function getTranslatedAttributes(): array
    {
        return [];
    }
}
