<?php

namespace A17\Twill\Repositories;

use A17\Twill\Models\Group;
use A17\Twill\Models\User;
use A17\Twill\Repositories\Behaviors\HandleMedias;
use A17\Twill\Repositories\Behaviors\HandlePermissions;
use DB;
use Password;

class UserRepository extends ModuleRepository
{
    use HandleMedias, HandlePermissions;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function getFormFields($user)
    {
        $fields = parent::getFormFields($user);
        $fields['browsers']['groups'] = $this->getFormFieldsForBrowser($user, 'groups');
        // Added everyone group to the beginning if the user's role included in everyone group
        if ($user->role->in_everyone_group) {
            $everyoneGroup = Group::getEveryoneGroup();
            array_unshift($fields['browsers']['groups'], [
                'id' => null,
                'name' => $everyoneGroup->name,
                'edit' => null,
                "endpointType" => "A17\Twill\Models\Group",
                "thumbnail" => "data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7",
                "deletable" => false,
            ]);
        }
        return $fields;
    }

    public function filter($query, array $scopes = [])
    {
        $query->when(isset($scopes['role']), function ($query) use ($scopes) {
            $query->where('role', $scopes['role']);
        });
        $query->where('is_superadmin', '<>', true);
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
        return $this->model->where('is_superadmin', '<>', true)->published()->count();
    }

    public function getCountForDraft()
    {
        return $this->model->where('is_superadmin', '<>', true)->draft()->count();
    }

    public function getCountForTrash()
    {
        return $this->model->where('is_superadmin', '<>', true)->onlyTrashed()->count();
    }

    public function prepareFieldsBeforeSave($user, $fields)
    {
        $fields = parent::prepareFieldsBeforeSave($user, $fields);
        if ($user->role->in_everyone_group && isset($fields['browsers']['groups'])) {
            $fields['browsers']['groups'] = array_filter($fields['browsers']['groups'], function ($group) {
                return $group['id'] && $group['name'] !== 'Everyone';
            });
        }
        return $fields;
    }

    public function afterSave($user, $fields)
    {
        $this->sendWelcomeEmail($user);

        $this->updateBrowser($user, $fields, 'groups');
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
}
