<?php

namespace A17\Twill\Models;

use Cartalyst\Tags\IlluminateTag;

class Tag extends IlluminateTag
{
    public $appends = ['value'];

    public function getValueAttribute()
    {
        return $this->name;
    }
}
