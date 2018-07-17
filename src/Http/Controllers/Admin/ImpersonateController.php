<?php

namespace A17\Twill\Http\Controllers\Admin;

use A17\Twill\Repositories\UserRepository;
use Auth;

class ImpersonateController extends Controller
{
    public function impersonate($id, UserRepository $users)
    {
        if (Auth::user()->can('impersonate')) {
            $user = $users->getById($id);
            Auth::user()->setImpersonating($user->id);
        }

        return back();
    }

    public function stopImpersonate()
    {
        Auth::user()->stopImpersonating();

        return back();
    }
}
