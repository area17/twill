<?php

namespace A17\CmsToolkit\Repositories;

use A17\CmsToolkit\Models\User;
use A17\CmsToolkit\Repositories\Behaviors\HandleMedias;
use Password;

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

        $query->where('role', '<>', 'SUPERADMIN');

        return parent::filter($query, $scopes);
    }

    public function afterSave($user, $fields)
    {
        if (empty($user->password)) {
            $user->sendWelcomeNotification(
                Password::getRepository()->create($user)
            );
        }
    }
}
