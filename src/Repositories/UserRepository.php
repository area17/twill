<?php

namespace A17\CmsToolkit\Repositories;

use A17\CmsToolkit\Models\User;
use A17\CmsToolkit\Repositories\Behaviors\HandleMedias;

class UserRepository extends ModuleRepository
{
    use HandleMedias;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function filter($query, array $scopes = [])
    {
        $this->addLikeFilterScope($query, $scopes, 'name');

        return parent::filter($query, $scopes);
    }
}
