<?php

namespace Sb4yd3e\Twill\Models;

use Sb4yd3e\Twill\Models\Behaviors\HasFiles;
use Sb4yd3e\Twill\Models\Behaviors\HasMedias;
use Sb4yd3e\Twill\Models\Behaviors\HasPresenter;
use Illuminate\Database\Eloquent\Model as BaseModel;

class Block extends BaseModel
{
    use HasMedias, HasFiles, HasPresenter;

    public $timestamps = false;

    protected $fillable = [
        'blockable_id',
        'blockable_type',
        'position',
        'content',
        'type',
        'child_key',
        'parent_id',
    ];

    protected $casts = [
        'content' => 'array',
    ];

    protected $with = ['medias'];

    public function blockable()
    {
        return $this->morphTo();
    }

    public function children()
    {
        return $this->hasMany('Sb4yd3e\Twill\Models\Block', 'parent_id');
    }

    public function input($name)
    {
        return $this->content[$name] ?? null;
    }

    public function translatedInput($name, $forceLocale = null)
    {
        $value = $this->content[$name] ?? null;
        $locale = $forceLocale ?? app()->getLocale();
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
        if (($presenter = config('twill.block_editor.block_presenter_path')) != null) {
            return $presenter;
        }

        return null;
    }
}
