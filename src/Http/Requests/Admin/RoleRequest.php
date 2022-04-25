<?php

namespace A17\Twill\Http\Requests\Admin;

class RoleRequest extends Request
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
