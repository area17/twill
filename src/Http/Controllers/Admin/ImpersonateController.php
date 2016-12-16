<?php

namespace A17\CmsToolkit\Http\Controllers\Admin;

use A17\CmsToolkit\Helpers\FlashLevel;
use A17\CmsToolkit\Repositories\UserRepository;
use Auth;

class ImpersonateController extends Controller
{
    public function impersonate($id, UserRepository $users)
    {
        if (Auth::user()->can('impersonate')) {
            $user = $users->getById($id);
            Auth::user()->setImpersonating($user->id);
        }

        flash()->message('You are now impersonating user with id ' . $id, FlashLevel::WARNING);
        return back();
    }

    public function stopImpersonate()
    {
        Auth::user()->stopImpersonating();
        return back();
    }
}
