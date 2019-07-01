<?php

namespace A17\Twill\Http\Controllers\Admin;

use A17\Twill\Repositories\UserRepository;
use Illuminate\Auth\AuthManager;
use Illuminate\Config\Repository as Config;
use Illuminate\Contracts\Foundation\Application;

class ImpersonateController extends Controller
{
    /**
     * @var AuthManager
     */
    protected $authManager;

    /**
     * @param Application $app
     * @param Config $config
     * @param AuthManager $authManager
     */
    public function __construct(Application $app, Config $config, AuthManager $authManager)
    {
        parent::__construct($app, $config);

        $this->authManager = $authManager;
    }

    public function impersonate($id, UserRepository $users)
    {
        if ($this->authManager->guard('twill_users')->user()->can('impersonate')) {
            $user = $users->getById($id);
            $this->authManager->guard('twill_users')->user()->setImpersonating($user->id);
        }

        return back();
    }

    public function stopImpersonate()
    {
        $this->authManager->guard('twill_users')->user()->stopImpersonating();
        return back();
    }
}
