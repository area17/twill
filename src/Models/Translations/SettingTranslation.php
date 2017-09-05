<?php

namespace A17\CmsToolkit\Models\Translations;

use A17\CmsToolkit\Models\Model;

class SettingTranslation extends Model
{
    protected $fillable = [
        'value',
        'active',
        'locale',
    ];
}
