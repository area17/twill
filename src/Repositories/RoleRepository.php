<?php

namespace A17\Twill\Repositories;

use A17\Twill\Models\Role;

class RoleRepository extends ModuleRepository
{
    public function __construct(Role $model)
    {
        $this->model = $model;
    }
}
