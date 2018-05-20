<?php

namespace A17\Twill\Models\Translations;

use A17\Twill\Models\Model;

class SettingTranslation extends Model
{
    protected $fillable = [
        'value',
        'active',
        'locale',
    ];
}
