<?php

namespace A17\Twill\Repositories;

use A17\Twill\Models\User;
use A17\Twill\Repositories\Behaviors\HandleMedias;
use DB;
use Password;

class UserRepository extends ModuleRepository
{
    use HandleMedias;

    /**
     * @param User $model
     */
    public function __construct(User $model)
    {
        $this->model = $model;
    }

    /**
     * @param \Illuminate\Database\Query\Builder $query
     * @param array $scopes
     * @return \Illuminate\Database\Query\Builder
     */
    public function filter($query, array $scopes = [])
    {
        $query->when(isset($scopes['role']), function ($query) use ($scopes) {
            $query->where('role', $scopes['role']);
        });
        $query->where('role', '<>', 'SUPERADMIN');
        $this->searchIn($query, $scopes, 'search', ['name', 'email', 'role']);
        return parent::filter($query, $scopes);
    }

    /**
     * @param \A17\Twill\Models\Model $user
     * @param array $fields
     */
    public function afterUpdateBasic($user, $fields)
    {
        $this->sendWelcomeEmail($user);
        parent::afterUpdateBasic($user, $fields);
    }

    /**
     * @return int
     */
    public function getCountForPublished()
    {
        return $this->model->where('role', '<>', 'SUPERADMIN')->published()->count();
    }

    /**
     * @return int
     */
    public function getCountForDraft()
    {
        return $this->model->where('role', '<>', 'SUPERADMIN')->draft()->count();
    }

    /**
     * @return int
     */
    public function getCountForTrash()
    {
        return $this->model->where('role', '<>', 'SUPERADMIN')->onlyTrashed()->count();
    }

    /**
     * @param \A17\Twill\Models\Model $user
     * @param array $fields
     * @return string[]
     */
    public function prepareFieldsBeforeSave($user, $fields)
    {
        $editor = auth('twill_users')->user();
        $with2faSettings = config('twill.enabled.users-2fa', false) && $editor->id === $user->id;

        if ($with2faSettings
            && $user->google_2fa_enabled
            && !($fields['google_2fa_enabled'] ?? false)
        ) {
            $fields['google_2fa_secret'] = null;
        }

        return parent::prepareFieldsBeforeSave($user, $fields);
    }

    /**
     * @param \A17\Twill\Models\Model|User $user
     * @param array $fields
     * @return void
     */
    public function afterSave($user, $fields)
    {
        $this->sendWelcomeEmail($user);
        parent::afterSave($user, $fields);
    }

    /**
     * @param User $user
     * @return void
     */
    private function sendWelcomeEmail($user)
    {
        if (empty($user->password) && $user->published && !DB::table(config('twill.password_resets_table', 'twill_password_resets'))->where('email', $user->email)->exists()) {
            $user->sendWelcomeNotification(
                Password::broker('twill_users')->getRepository()->create($user)
            );
        }
    }
}
