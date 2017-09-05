<?php

namespace A17\CmsToolkit\Models;

use A17\CmsToolkit\Models\Behaviors\HasTranslation;
use A17\CmsToolkit\Models\Model;

class Setting extends Model
{
    use HasTranslation;

    public $useTranslationFallback = true;

    protected $fillable = [
        'key',
        'section',
    ];

    public $translatedAttributes = [
        'value',
        'locale',
        'active',
    ];

    public function getTranslationModelNameDefault()
    {
        return "A17\CmsToolkit\Models\Translations\SettingTranslation";
    }
}
