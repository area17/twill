<?php

namespace A17\Twill\Models;

use A17\Twill\Models\Behaviors\HasTranslation;
use A17\Twill\Models\Model;

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
        return "A17\Twill\Models\Translations\SettingTranslation";
    }
}
