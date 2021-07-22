<?php

namespace A17\Twill\Repositories;

use A17\Twill\Models\Role;
use A17\Twill\Repositories\Behaviors\HandleRolePermissions;

class RoleRepository extends ModuleRepository
{
    use HandleRolePermissions;

    public function __construct(Role $model)
    {
        $this->model = $model;
    }

    public function filter($query, array $scopes = [])
    {
        $this->searchIn($query, $scopes, 'search', ['name']);

        return parent::filter($query, $scopes);
    }
}
