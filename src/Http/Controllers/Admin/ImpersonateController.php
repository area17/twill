<?php

namespace A17\Twill\Http\Controllers\Admin;

use A17\Twill\Repositories\UserRepository;
use Illuminate\Auth\AuthManager;

class ImpersonateController extends Controller
{
    /**
     * @var AuthManager
     */
    protected $authManager;

    public function __construct(AuthManager $authManager)
    {
        parent::__construct();

        $this->authManager = $authManager;
    }

    /**
     * @param int $id
     * @param UserRepository $users
     * @return \Illuminate\Http\RedirectResponse
     */
    public function impersonate($id, UserRepository $users)
    {
        if ($this->authManager->guard('twill_users')->user()->can('impersonate')) {
            $user = $users->getById($id);
            $this->authManager->guard('twill_users')->user()->setImpersonating($user->id);
        }

        return back();
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function stopImpersonate()
    {
        $this->authManager->guard('twill_users')->user()->stopImpersonating();
        return back();
    }
}
