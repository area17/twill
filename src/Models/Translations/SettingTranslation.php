<?php

namespace Sb4yd3e\Twill\Models\Translations;

use Sb4yd3e\Twill\Models\Model;

class SettingTranslation extends Model
{
    protected $fillable = [
        'value',
        'active',
        'locale',
    ];
}
