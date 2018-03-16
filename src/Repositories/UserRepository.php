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
        $query->when(isset($scopes['role']), function ($query) use ($scopes) {
            $query->where('role', $scopes['role']);
        });
        $query->where('role', '<>', 'SUPERADMIN');
        $this->searchIn($query, $scopes, 'search', ['name', 'email', 'role']);
        return parent::filter($query, $scopes);
    }

    public function afterUpdateBasic($user, $fields)
    {
        $this->sendWelcomeEmail($user);
        parent::afterUpdateBasic($user, $fields);
    }

    public function getCountForPublished()
    {
        return $this->model->where('role', '<>', 'SUPERADMIN')->published()->count();
    }

    public function getCountForDraft()
    {
        return $this->model->where('role', '<>', 'SUPERADMIN')->draft()->count();
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
