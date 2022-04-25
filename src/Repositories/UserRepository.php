<?php

namespace A17\Twill\Repositories;

use A17\Twill\Models\User;
use A17\Twill\Models\Group;
use A17\Twill\Repositories\Behaviors\HandleMedias;
use A17\Twill\Repositories\Behaviors\HandleOauth;
use A17\Twill\Repositories\Behaviors\HandleUserPermissions;
use Carbon\Carbon;
use Illuminate\Auth\Passwords\PasswordBrokerManager;
use Illuminate\Config\Repository as Config;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Database\DatabaseManager as DB;

class UserRepository extends ModuleRepository
{
    use HandleMedias;
    use HandleOauth;
    use HandleUserPermissions;
    public function __construct(
        protected DB $db,
        protected Config $config,
        protected PasswordBrokerManager $passwordBrokerManager,
        protected AuthFactory $authFactory
    ) {
        $userModel = twillModel('user');
        $this->model = new $userModel;
    }

    /**
     * @return mixed[]
     */
    public function getFormFields($user): array
    {
        $fields = parent::getFormFields($user);

        if ($user->isSuperAdmin()) {
            return $fields;
        }

        if (config('twill.enabled.permissions-management')) {
            $fields['browsers']['groups'] = $this->getFormFieldsForBrowser($user, 'groups');
        }

        return $fields;
    }

    public function filter(\Illuminate\Database\Eloquent\Builder $query, array $scopes = []): \Illuminate\Database\Eloquent\Builder
    {
        if (config('twill.enabled.permissions-management')) {
            $query->where('is_superadmin', '<>', true);
            $this->searchIn($query, $scopes, 'search', ['name', 'email']);
        } else {
            $query->when(isset($scopes['role']), function ($query) use ($scopes): void {
                $query->where('role', $scopes['role']);
            });
            $query->where('role', '<>', 'SUPERADMIN');
            $this->searchIn($query, $scopes, 'search', ['name', 'email', 'role']);
        }

        return parent::filter($query, $scopes);
    }

    /**
     * @return mixed[]
     */
    public function getFormFieldsForBrowser(\A17\Twill\Models\Model $object, string $relation, $routePrefix = null, string $titleKey = 'title', $moduleName = null): array
    {
        $browserFields = parent::getFormFieldsForBrowser($object, $relation, $routePrefix, $titleKey, $moduleName);

        if (config('twill.enabled.permissions-management')) {
            foreach ($browserFields as $index => $browserField) {
                if ($browserField['id'] === Group::getEveryoneGroup()->id &&
                    $browserField['name'] === Group::getEveryoneGroup()->name
                ) {
                    $browserFields[$index]['edit'] = false;
                    $browserFields[$index]['deletable'] = false;
                }
            }
        }

        return $browserFields;
    }

    /**
     * @param mixed[] $fields
     */
    public function afterUpdateBasic(\A17\Twill\Models\Model $user, array $fields): void
    {
        $this->sendWelcomeEmail($user);
        parent::afterUpdateBasic($user, $fields);
    }

    /**
     * @deprecated To be removed in Twill 3.0
     */
    public function getCountForAll(): int
    {
        return $this->model->notSuperAdmin()->count();
    }

    /**
     * @deprecated To be removed in Twill 3.0
     */
    public function getCountForPublished(): int
    {
        return $this->model->notSuperAdmin()->published()->count();
    }

    /**
     * @deprecated To be removed in Twill 3.0
     */
    public function getCountForDraft(): int
    {
        return $this->model->notSuperAdmin()->draft()->count();
    }

    /**
     * @deprecated To be removed in Twill 3.0
     */
    public function getCountForTrash(): int
    {
        return $this->model->notSuperAdmin()->onlyTrashed()->count();
    }

    /**
     * @return string[]
     * @param mixed[] $fields
     */
    public function prepareFieldsBeforeSave(\A17\Twill\Models\Model $user, array $fields): array
    {
        $editor = $this->authFactory->guard('twill_users')->user();
        $with2faSettings = $this->config->get('twill.enabled.users-2fa', false) && $editor->id === $user->id;

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
     * @param mixed[] $fields
     */
    public function afterSave($user, array $fields): void
    {
        $this->sendWelcomeEmail($user);

        if (!empty($fields['reset_password']) && !empty($fields['new_password'])) {
            $user->password = bcrypt($fields['new_password']);

            if (!$user->isActivated()) {
                $user->registered_at = Carbon::now();
            }

            if (!empty($fields['require_password_change'])) {
                $user->require_new_password = true;
                $user->sendTemporaryPasswordNotification($fields['new_password']);
            } else {
                $user->sendPasswordResetByAdminNotification($fields['new_password']);
            }

            $user->save();
        }

        if (config('twill.enabled.permissions-management')
            && auth('twill_users')->user()->can('edit-user-groups')
        ) {
            $this->updateBrowser($user, $fields, 'groups');
        }

        parent::afterSave($user, $fields);
    }

    private function sendWelcomeEmail(\A17\Twill\Models\User $user): void
    {
        if (empty($user->password)
            && $user->isPublished()
            && !$this->db
            ->table($this->config->get('twill.password_resets_table', 'twill_password_resets'))
            ->where('email', $user->email)
            ->exists()
        ) {
            $user->sendWelcomeNotification(
                $this->passwordBrokerManager->broker('twill_users')->getRepository()->create($user)
            );
        }
    }

}
