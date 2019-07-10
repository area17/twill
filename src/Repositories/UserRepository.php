<?php

namespace A17\Twill\Repositories;

use A17\Twill\Models\Group;
use A17\Twill\Models\User;
use A17\Twill\Repositories\Behaviors\HandleMedias;
use DB;
use Password;
use Carbon\Carbon;

class UserRepository extends ModuleRepository
{
    use HandleMedias;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function getFormFields($user)
    {
        $fields = parent::getFormFields($user);

        if ($user->is_superadmin) {
            return $fields;
        }

        $fields['browsers']['groups'] = $this->getFormFieldsForBrowser($user, 'groups');
        // Added everyone group to the beginning if the user's role included in everyone group
        if ($user->role->in_everyone_group) {
            array_unshift($fields['browsers']['groups'], [
                'id' => null,
                'name' => 'Everyone',
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
        
        // Do not handle the everyone group
        if (isset($fields['browsers']['groups'])) {
            $fields['browsers']['groups'] = array_filter($fields['browsers']['groups'], function ($group) {
                return $group['name'] !== 'Everyone';
            });
        }

        $editor = auth('twill_users')->user();
        $with2faSettings = config('twill.enabled.users-2fa', false) && $editor->id === $user->id;

        if ($with2faSettings
            && $user->google_2fa_enabled
            && !($fields['google_2fa_enabled'] ?? false)
        ) {
            $fields['google_2fa_secret'] = null;
        }

        return $fields;
    }

    public function afterSave($user, $fields)
    {
        $this->sendWelcomeEmail($user);
        $this->updateBrowser($user, $fields, 'groups');

        if (!empty($fields['reset_password']) && !empty($fields['new_password'])) {
            $user->password = bcrypt($fields['new_password']);
            
            if (!$user->activate) {
                $user->activated = true;
                $user->registered_at = Carbon::now();
            }

            if (!empty($fields['require_password_change'])) {
                $user->require_new_password = true;
            }

            $user->save();
        }

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
