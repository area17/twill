<?php

namespace A17\CmsToolkit\Models;

use Cartalyst\Tags\IlluminateTag;

class Tag extends IlluminateTag
{
    public $appends = ['value'];

    public function getValueAttribute()
    {
        return $this->name;
    }
}
