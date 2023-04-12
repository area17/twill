<?php

namespace A17\Twill\Models;

use A17\Twill\Models\Behaviors\HasMedias;
use A17\Twill\Models\Behaviors\HasTranslation;
use Illuminate\Support\Str;

class Setting extends Model
{
    use HasTranslation;
    use HasMedias;

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

    public function getTable()
    {
        return config('twill.settings_table', 'twill_settings');
    }

    protected function getTranslationRelationKey(): string
    {
        return Str::singular(config('twill.settings_table', 'twill_settings')) . '_id';
    }
}
