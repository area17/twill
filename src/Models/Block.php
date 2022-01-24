<?php

namespace A17\Twill\Models;

use A17\Twill\Models\Behaviors\HasFiles;
use A17\Twill\Models\Behaviors\HasMedias;
use A17\Twill\Models\Behaviors\HasPresenter;
use A17\Twill\Models\Behaviors\HasRelated;
use Illuminate\Database\Eloquent\Model as BaseModel;

class Block extends BaseModel
{
    use HasMedias, HasFiles, HasPresenter, HasRelated;

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

    protected $with = ['medias'];

    public function scopeEditor($query, $name = 'default')
    {
        return $name === 'default' ?
            $query->where('editor_name', $name)->orWhereNull('editor_name') :
            $query->where('editor_name', $name);
    }

    public function blockable()
    {
        return $this->morphTo();
    }

    public function children()
    {
        return $this->hasMany('A17\Twill\Models\Block', 'parent_id');
    }

    public function input($name)
    {
        return $this->content[$name] ?? null;
    }

    public function translatedInput($name, $forceLocale = null)
    {
        $value = $this->content[$name] ?? null;

        $locale = $forceLocale ?? (
            config('translatable.use_property_fallback', false) && (!array_key_exists(app()->getLocale(), $value ?? []))
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

    public function renderBlocks($editor, $renderChilds = false, $blockViewMappings = [], $data = []): string
    {
        return $this->renderNamedBlocks($editor, $renderChilds, $blockViewMappings, $data);
    }

    public function renderNamedBlocks($editor, $renderChilds = false, $blockViewMappings = [], $data = []): string
    {
        $blocks = self::where('parent_id', $this->id)->where('child_key', $editor)->get();

        return $blocks->map(function ($block) use ($blockViewMappings, $renderChilds, $data) {
                if ($renderChilds) {
                    $childBlocks = self::where('parent_id', $block->id)->get();

                    $renderedChildViews = $childBlocks->map(function ($childBlock) use ($blockViewMappings, $data) {
                        $view = $this->getBlockView($childBlock->type, $blockViewMappings);
                        return view($view, $data)->with('block', $childBlock)->render();
                    })->implode('');
                }

                $block->childs = self::where('parent_id', $block->id)->get();

                $view = $this->getBlockView($block->type, $blockViewMappings);

                return view($view, $data)->with('block', $block)->render() . ($renderedChildViews ?? '');
            })->implode('');
    }

    private function getBlockView($blockType, $blockViewMappings = []): string
    {
        $view = config('twill.block_editor.block_views_path') . '.' . $blockType;

        if (array_key_exists($blockType, $blockViewMappings)) {
            $view = $blockViewMappings[$blockType];
        }

        return $view;
    }

    public function getPresenterAttribute()
    {
        if (($presenter = config('twill.block_editor.block_presenter_path')) != null) {
            return $presenter;
        }

        return null;
    }

    public function getTable()
    {
        return config('twill.blocks_table', 'twill_blocks');
    }
}
