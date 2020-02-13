<?php

namespace A17\Twill\Models\Translations;

use A17\Twill\Models\Model;
use Illuminate\Support\Str;

class SettingTranslation extends Model
{
    protected $fillable = [
        'value',
        'active',
        'locale',
    ];

    public function getTable()
    {
        $twillSettingsTable = config('twill.settings_table', 'twill_settings');

        return Str::singular($twillSettingsTable) . '_translations';
    }
}
