<?php

namespace A17\Twill\Http\Requests\Admin;

class GroupRequest extends Request
{
    /**
     * @return mixed[]
     */
    public function rulesForCreate(): array
    {
        return [];
    }

    /**
     * @return mixed[]
     */
    public function rulesForUpdate(): array
    {
        return [];
    }
}
