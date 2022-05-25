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
    use HandleMedias, HandleOauth, HandleUserPermissions;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var DB
     */
    protected $db;

    /**
     * @var PasswordBrokerManager
     */
    protected $passwordBrokerManager;

    /**
     * @var AuthFactory
     */
    protected $authFactory;

    /**
     * @param DB $db
     * @param Config $config
     * @param PasswordBrokerManager $passwordBrokerManager
     * @param AuthFactory $authFactory
     */
    public function __construct(
        DB $db,
        Config $config,
        PasswordBrokerManager $passwordBrokerManager,
        AuthFactory $authFactory
    ) {
        $userModel = twillModel('user');
        $this->model = new $userModel;
        $this->passwordBrokerManager = $passwordBrokerManager;
        $this->authFactory = $authFactory;
        $this->config = $config;
        $this->db = $db;
    }

    public function getFormFields($user)
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

    /**
     * @param \Illuminate\Database\Query\Builder $query
     * @param array $scopes
     * @return \Illuminate\Database\Query\Builder
     */
    public function filter($query, array $scopes = [])
    {
        if (config('twill.enabled.permissions-management')) {
            $query->where('is_superadmin', '<>', true);
            $this->searchIn($query, $scopes, 'search', ['name', 'email']);
        } else {
            $query->when(isset($scopes['role']), function ($query) use ($scopes) {
                $query->where('role', $scopes['role']);
            });
            $query->where('role', '<>', 'SUPERADMIN');
            $this->searchIn($query, $scopes, 'search', ['name', 'email', 'role']);
        }
        return parent::filter($query, $scopes);
    }

    public function getFormFieldsForBrowser($object, $relation, $routePrefix = null, $titleKey = 'title', $moduleName = null)
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
     * @param \A17\Twill\Models\Model $user
     * @param array $fields
     */
    public function afterUpdateBasic($user, $fields)
    {
        $this->sendWelcomeEmail($user);
        parent::afterUpdateBasic($user, $fields);
    }

    /**
     * @deprecated To be removed in Twill 3.0
     * @return int
     */
    public function getCountForAll()
    {
        return $this->model->notSuperAdmin()->count();
    }

    /**
     * @deprecated To be removed in Twill 3.0
     * @return int
     */
    public function getCountForPublished()
    {
        return $this->model->notSuperAdmin()->published()->count();
    }

    /**
     * @deprecated To be removed in Twill 3.0
     * @return int
     */
    public function getCountForDraft()
    {
        return $this->model->notSuperAdmin()->draft()->count();
    }

    /**
     * @deprecated To be removed in Twill 3.0
     * @return int
     */
    public function getCountForTrash()
    {
        return $this->model->notSuperAdmin()->onlyTrashed()->count();
    }

    /**
     * @param \A17\Twill\Models\Model $user
     * @param array $fields
     * @return string[]
     */
    public function prepareFieldsBeforeSave($user, $fields)
    {
        $editor = $this->authFactory->guard('twill_users')->user();
        $with2faSettings = $this->config->get('twill.enabled.users-2fa', false) && $editor->id === $user->id;

        if ($with2faSettings
            && $user->google_2fa_enabled
            && !($fields['google_2fa_enabled'] ?? false)
        ) {
            $fields['google_2fa_secret'] = null;
        }

        if ($this->config->get('twill.enabled.users-2fa', false)
            && ($fields['force-2fa-disable-challenge'] ?? false)) {
            $user->google_2fa_enabled = false;
            $user->google_2fa_secret = null;
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

    /**
     * @param User $user
     * @return void
     */
    private function sendWelcomeEmail($user)
    {
        if (empty($user->password)
            && $user->published
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
