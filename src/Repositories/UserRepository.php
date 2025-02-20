<?php

namespace A17\Twill\Repositories;

use A17\Twill\Facades\TwillPermissions;
use A17\Twill\Models\Contracts\TwillModelContract;
use A17\Twill\Models\User;
use A17\Twill\Repositories\Behaviors\HandleMedias;
use A17\Twill\Repositories\Behaviors\HandleOauth;
use A17\Twill\Repositories\Behaviors\HandleUserPermissions;
use Carbon\Carbon;
use Illuminate\Auth\Passwords\PasswordBrokerManager;
use Illuminate\Config\Repository as Config;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Database\DatabaseManager as DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class UserRepository extends ModuleRepository
{
    use HandleMedias;
    use HandleOauth;
    use HandleUserPermissions;

    protected Config $config;

    protected DB $db;

    protected PasswordBrokerManager $passwordBrokerManager;

    protected AuthFactory $authFactory;

    public function __construct(
        DB $db,
        Config $config,
        PasswordBrokerManager $passwordBrokerManager,
        AuthFactory $authFactory
    ) {
        $userModel = twillModel('user');
        $this->model = new $userModel();
        $this->passwordBrokerManager = $passwordBrokerManager;
        $this->authFactory = $authFactory;
        $this->config = $config;
        $this->db = $db;
    }

    public function getFormFields(TwillModelContract|User $user): array
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

    public function filter(Builder $query, array $scopes = []): Builder
    {
        if (TwillPermissions::enabled()) {
            $query->where('is_superadmin', '<>', true);
        } else {
            $query->where('role', '<>', 'SUPERADMIN');
        }
        return parent::filter($query, $scopes);
    }

    public function getFormFieldsForBrowser(
        $object,
        $relation,
        $routePrefix = null,
        $titleKey = 'title',
        $moduleName = null
    ) {
        $browserFields = parent::getFormFieldsForBrowser($object, $relation, $routePrefix, $titleKey, $moduleName);

        if (TwillPermissions::enabled()) {
            $everyoneGroup = twillModel('group')::getEveryoneGroup();
            foreach ($browserFields as $index => $browserField) {
                if (
                    $browserField['id'] === $everyoneGroup->id &&
                    $browserField['name'] === $everyoneGroup->name
                ) {
                    $browserFields[$index]['edit'] = false;
                    $browserFields[$index]['deletable'] = false;
                }
            }
        }
        return $browserFields;
    }

    public function afterUpdateBasic(TwillModelContract|User $user, array $fields): void
    {
        $this->sendWelcomeEmail($user);
        parent::afterUpdateBasic($user, $fields);
    }

    public function prepareFieldsBeforeSave(TwillModelContract|User $user, array $fields): array
    {
        /** @var \A17\Twill\Models\User $editor */
        $editor = $this->authFactory->guard('twill_users')->user();
        $with2faSettings = $this->config->get('twill.enabled.users-2fa', false) && $editor?->id === $user->id;

        if (
            $with2faSettings
            && $user->google_2fa_enabled
            && !($fields['google_2fa_enabled'] ?? false)
        ) {
            $fields['google_2fa_secret'] = null;
        }

        if (
            $this->config->get('twill.enabled.users-2fa', false)
            && ($fields['force-2fa-disable-challenge'] ?? false)
        ) {
            $user->google_2fa_enabled = false;
            $user->google_2fa_secret = null;
        }

        return parent::prepareFieldsBeforeSave($user, $fields);
    }

    public function afterSave(TwillModelContract|user $user, array $fields): void
    {
        $this->sendWelcomeEmail($user);

        if (!empty($fields['reset_password']) && !empty($fields['new_password'])) {
            $user->password = Hash::make($fields['new_password']);

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

        if (TwillPermissions::enabled() && auth('twill_users')->user()->can('edit-user-groups')) {
            $this->updateBrowser($user, $fields, 'groups');
        }

        parent::afterSave($user, $fields);
    }

    private function sendWelcomeEmail(User $user): void
    {
        if (
            empty($user->password)
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
