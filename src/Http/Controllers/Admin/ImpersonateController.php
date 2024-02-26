<?php

namespace A17\Twill\Http\Controllers\Admin;

use A17\Twill\Repositories\UserRepository;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\RedirectResponse;

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
     * @param  int  $id
     * @return RedirectResponse
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
     * @return RedirectResponse
     */
    public function stopImpersonate()
    {
        $this->authManager->guard('twill_users')->user()->stopImpersonating();

        return back();
    }
}
