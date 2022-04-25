<?php

namespace A17\Twill\Models\Translations;

use A17\Twill\Models\Model;
use Illuminate\Support\Str;

class SettingTranslation extends Model
{
    /**
     * @var array<class-string<\locale>>|string[]
     */
    protected $fillable = [
        'value',
        'active',
        'locale',
    ];

    public function getTable(): string
    {
        $twillSettingsTable = config('twill.settings_table', 'twill_settings');

        return Str::singular($twillSettingsTable) . '_translations';
    }
}
