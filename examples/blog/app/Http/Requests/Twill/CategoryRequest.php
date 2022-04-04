<?php

namespace App\Http\Requests\Twill;

use A17\Twill\Http\Requests\Admin\Request;

class CategoryRequest extends Request
{
    public function rulesForCreate()
    {
        return [];
    }

    public function rulesForUpdate()
    {
        return [];
    }
}
