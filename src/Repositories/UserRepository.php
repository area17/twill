<?php

namespace A17\CmsToolkit\Repositories;

use A17\CmsToolkit\Models\User;
use A17\CmsToolkit\Repositories\Behaviors\HandleMedias;
use DB;
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
        return parent::filter($query, $scopes);
    }

    public function afterUpdateBasic($user, $fields)
    {
        $this->sendWelcomeEmail($user);
        parent::afterUpdateBasic($user, $fields);
    }

    public function afterSave($user, $fields)
    {
        $this->sendWelcomeEmail($user);
        parent::afterSave($user, $fields);
    }

    private function sendWelcomeEmail($user)
    {
        if (empty($user->password) && $user->published && !DB::table('password_resets')->where('email', $user->email)->exists()) {
            $user->sendWelcomeNotification(
                Password::getRepository()->create($user)
            );
        }
    }
}
