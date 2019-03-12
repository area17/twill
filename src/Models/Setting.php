<?php

namespace Sb4yd3e\Twill\Models;

use Sb4yd3e\Twill\Models\Behaviors\HasTranslation;
use Sb4yd3e\Twill\Models\Behaviors\HasMedias;
use Sb4yd3e\Twill\Models\Model;

class Setting extends Model
{
    use HasTranslation, HasMedias;

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
        return "Sb4yd3e\Twill\Models\Translations\SettingTranslation";
    }
}
