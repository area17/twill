<?php

namespace A17\Twill\Repositories;

use A17\Twill\Models\User;
use A17\Twill\Repositories\Behaviors\HandleMedias;
use DB;
use Password;
use App\Models\Guide;

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

    public function getCountForTrash()
    {
        return $this->model->where('role', '<>', 'SUPERADMIN')->onlyTrashed()->count();
    }

    public function afterSave($user, $fields)
    {
        $this->sendWelcomeEmail($user);
        $this->handleGuidePermissions($user, $fields);
        parent::afterSave($user, $fields);
    }

    private function sendWelcomeEmail($user)
    {
        if (empty($user->password) && $user->published && !DB::table(config('twill.password_resets_table', 'twill_password_resets'))->where('email', $user->email)->exists()) {
            $user->sendWelcomeNotification(
                Password::broker('twill_users')->getRepository()->create($user)
            );
        }
    }

    private function handleGuidePermissions($user, $fields)
    {
        foreach($fields as $key => $value) {
            if(starts_with($key, 'guideId')) {
                $guideId = explode('_', $key)[1];
                $guide = Guide::find($guideId);
            }
        }
    }

}
