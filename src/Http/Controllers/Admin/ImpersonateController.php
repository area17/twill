<?php

namespace A17\Twill\Http\Controllers\Admin;

use A17\Twill\Repositories\UserRepository;
use Illuminate\Auth\AuthManager;

class ImpersonateController extends Controller
{
    public function __construct(protected AuthManager $authManager)
    {
        parent::__construct();
    }

    public function impersonate(int $id, UserRepository $users): \Illuminate\Http\RedirectResponse
    {
        if ($this->authManager->guard('twill_users')->user()->can('impersonate')) {
            $user = $users->getById($id);
            $this->authManager->guard('twill_users')->user()->setImpersonating($user->id);
        }

        return back();
    }

    public function stopImpersonate(): \Illuminate\Http\RedirectResponse
    {
        $this->authManager->guard('twill_users')->user()->stopImpersonating();
        return back();
    }
}
