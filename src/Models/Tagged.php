<?php

namespace A17\Twill\Models;

use Cartalyst\Tags\IlluminateTagged;

class Tagged extends IlluminateTagged
{
    public function getTable()
    {
        return config('twill.tagged_table', 'tagged');
    }
}
