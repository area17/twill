<?php

namespace A17\Twill\Repositories;

use Carbon\Carbon;
use A17\Twill\Models\Group;
use A17\Twill\Models\User;
use A17\Twill\Models\Role;
use A17\Twill\Models\Permission;
use A17\Twill\Repositories\Behaviors\HandleMedias;
use Illuminate\Auth\Passwords\PasswordBrokerManager;
use Illuminate\Config\Repository as Config;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Database\DatabaseManager as DB;

class UserRepository extends ModuleRepository
{
    use HandleMedias;

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
     * @param User $model
     */
    public function __construct(
        DB $db,
        Config $config,
        PasswordBrokerManager $passwordBrokerManager,
        AuthFactory $authFactory,
        User $model
    ) {

        $this->model = $model;
        $this->passwordBrokerManager = $passwordBrokerManager;
        $this->authFactory = $authFactory;
        $this->config = $config;
        $this->db = $db;
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

    /**
     * @param \Illuminate\Database\Query\Builder $query
     * @param array $scopes
     * @return \Illuminate\Database\Query\Builder
     */
    public function filter($query, array $scopes = [])
    {
        $query->where('is_superadmin', '<>', true);
        $this->searchIn($query, $scopes, 'search', ['name', 'email', 'role']);
        return parent::filter($query, $scopes);
    }

    public function getFormFieldsForBrowser($object, $relation, $routePrefix = null, $titleKey = 'title', $moduleName = null)
    {
        $browserFields = parent::getFormFieldsForBrowser($object, $relation, $routePrefix, $titleKey, $moduleName);
        foreach ($browserFields as $index => $browserField) {
            if ($browserField['id'] === Group::getEveryoneGroup()->id && $browserField['name'] === Group::getEveryoneGroup()->name) {
                $browserFields[$index]['deletable'] = false;
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
     * @return int
     */
    public function getCountForPublished()
    {
        return $this->model->where('is_superadmin', '<>', true)->published()->count();
    }

    /**
     * @return int
     */
    public function getCountForDraft()
    {
        return $this->model->where('is_superadmin', '<>', true)->draft()->count();
    }

    /**
     * @return int
     */
    public function getCountForTrash()
    {
        return $this->model->where('is_superadmin', '<>', true)->onlyTrashed()->count();
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

        return $fields;
    }

    /**
     * @param \A17\Twill\Models\Model|User $user
     * @param array $fields
     * @return void
     */
    public function afterSave($user, $fields)
    {
        $this->sendWelcomeEmail($user);
        $this->updateMultiSelect($user, $fields, 'groups');

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
            $user->sendTemporaryPasswordNotification($fields['new_password']);
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
