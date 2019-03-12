<?php

namespace Sb4yd3e\Twill\Http\Controllers\Admin;

use Sb4yd3e\Twill\Repositories\UserRepository;
use Auth;

class ImpersonateController extends Controller
{
    public function impersonate($id, UserRepository $users)
    {
        if (Auth::guard('twill_users')->user()->can('impersonate')) {
            $user = $users->getById($id);
            Auth::guard('twill_users')->user()->setImpersonating($user->id);
        }

        return back();
    }

    public function stopImpersonate()
    {
        Auth::guard('twill_users')->user()->stopImpersonating();
        return back();
    }
}
