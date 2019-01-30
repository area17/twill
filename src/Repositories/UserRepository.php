<?php

namespace A17\Twill\Repositories;

use A17\Twill\Models\User;
use A17\Twill\Models\Permission;
use A17\Twill\Repositories\Behaviors\HandleMedias;
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

    public function getFormFields($user) {
        // don't forget to call the parent getFormFields function
        $fields = parent::getFormFields($user);
        foreach($user->permissions as $permission) {
            $module = $permission->permissionable()->first();
            $module_name = lcfirst(class_basename($module));
            $fields[$module_name . '_' . $module->id . '_permission'] = '"' . $permission->permission_name . '"';
        }
        return $fields;
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
        $this->handlePermissions($user, $fields);
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

    private function handlePermissions($user, $fields)
    {
        foreach($fields as $key => $value) {
            if(ends_with($key, '_permission')) {
                $key = explode('_', $key);
                $module_name = $key[0];
                $module_id = $key[1];
                $module = $this->getRepositoryByModuleName($module_name)->getById($module_id);

                // Qqery existed permission
                $permission = Permission::where([
                    ['permissionable_type', get_class($module)],
                    ['permissionable_id', $module->id],
                    ['twill_user_id', $user->id]
                ])->first() ?? new Permission;

                // only when value is not none, do update or create
                if ($value) {
                    $permission->permission_name = $value;
                    $permission->guard_name = $value;
                    $permission->permissionable()->associate($module);
                    $user->permissions()->save($permission);
                    $permission->save();
                }
                //if the existed permission has been set as none, delete it
                elseif ($permission) {
                    $permission->delete();
                }
            }
        }
    }

    protected function getRepositoryByModuleName($module)
    {
        return app(config('twill.namespace') . "\Repositories\\" . ucfirst(str_singular($module)) . "Repository");
    }

}
