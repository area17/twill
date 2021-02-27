<?php

namespace A17\Twill\Models;

use Cartalyst\Tags\IlluminateTag;

class Tag extends IlluminateTag
{
    protected static $taggedModel = Tagged::class;

    public function getTable()
    {
        return config('twill.tags_table', 'tags');
    }
}
