<?php

namespace A17\Twill\Repositories;

use A17\Twill\Models\User;
use A17\Twill\Repositories\Behaviors\HandleMedias;
use Illuminate\Auth\Passwords\PasswordBrokerManager;
use Illuminate\Config\Repository as Config;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Database\DatabaseManager as DB;
use Illuminate\Foundation\Application;
use Psr\Log\LoggerInterface as Logger;

class UserRepository extends ModuleRepository
{
    use HandleMedias;

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
     * @param Logger $logger
     * @param Application $app
     * @param Config $config
     * @param PasswordBrokerManager $passwordBrokerManager
     * @param AuthFactory $authFactory
     * @param User $model
     */
    public function __construct(
        DB $db,
        Logger $logger,
        Application $app,
        Config $config,
        PasswordBrokerManager $passwordBrokerManager,
        AuthFactory $authFactory,
        User $model
    ) {
        parent::__construct($db, $logger, $app, $config);

        $this->model = $model;
        $this->passwordBrokerManager = $passwordBrokerManager;
        $this->authFactory = $authFactory;
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

        return parent::prepareFieldsBeforeSave($user, $fields);
    }

    public function afterSave($user, $fields)
    {
        $this->sendWelcomeEmail($user);
        parent::afterSave($user, $fields);
    }

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
