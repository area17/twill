<?php

namespace A17\Twill\Repositories;

use Illuminate\Support\Arr;
use A17\Twill\Models\Group;
use A17\Twill\Models\User;
use A17\Twill\Models\Role;
use A17\Twill\Models\Permission;
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

        // When role changed, update it's groups information if needed.
        if (Role::findOrFail($fields['role_id'])->in_everyone_group) {
            $user->groups()->syncWithoutDetaching(Group::getEveryoneGroup()->id);
        } else {
            $user->groups()->detach(Group::getEveryoneGroup()->id);
        }

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

    // When the user is added to a new group, grant the user all permissions the group has.
    // public function updateBrowser($object, $fields, $relationship, $positionAttribute = 'position')
    // {
    //     // When the user is added into / removed from a group, grant / revoke all permissions that group has.
    //     if ($relationship === 'groups') {
    //         $currentGroupsIds = $object->groups->pluck('id')->toArray();
    //         $newGroupsIds = Arr::pluck($fields['browsers']['groups'], 'id');
            
    //         $addedGroupsIds = array_values(array_diff($newGroupsIds, $currentGroupsIds));
    //         $deletedGroupsIds = array_values(array_diff($currentGroupsIds, $newGroupsIds));

    //         if (!empty($addedGroupsIds)) {

    //             // All Items that the groups can view.
    //             $addedItems = Permission::whereHas('groups', function ($query) use ($addedGroupsIds) {
    //                 $query->whereIn('id', $addedGroupsIds);
    //             })->with('permissionable')->get()->pluck('permissionable');
                
    //             foreach($addedItems as $item) {
    //                 $object->grantModuleItemPermission('view-item', $item);
    //             }
    //         }

    //         if (!empty($deletedGroupsIds)) {
                
    //             // All Items that the groups can view.
    //             $deletedItems = Permission::whereHas('groups', function ($query) use ($deletedGroupsIds) {
    //                 $query->whereIn('id', $deletedGroupsIds);
    //             })->with('permissionable')->get()->pluck('permissionable');

    //             foreach($deletedItems as $item) {
    //                 $object->revokeModuleItemPermission('view-item', $item);
    //             }
    //         }
    //     }

    //     parent::updateBrowser($object, $fields, $relationship);
    // }
}
