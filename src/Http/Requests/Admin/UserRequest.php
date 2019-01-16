<?php

namespace A17\Twill\Http\Requests\Admin;

use Auth;
use Crypt;
use PragmaRX\Google2FA\Google2FA;

class UserRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        switch ($this->method()) {
            case 'POST':
                {
                    return [
                        'name' => 'required',
                        'email' => 'required|email|unique:' . config('twill.users_table', 'twill_users') . ',email',
                        'role' => 'required',
                    ];
                }
            case 'PUT':
                {
                    return [
                        'name' => 'required',
                        'email' => 'required|email|unique:' . config('twill.users_table', 'twill_users') . ',email,' . request('user'),
                        'verify-code' => function ($attribute, $value, $fail) {
                            $user = Auth::guard('twill_users')->user();
                            $with2faSettings = config('twill.enabled.users-2fa') && $user->id == request('user');

                            if ($with2faSettings) {
                                $userIsEnabling = request('google_2fa_enabled') && !$user->google_2fa_enabled;
                                $userIsDisabling = !request('google_2fa_enabled') && $user->google_2fa_enabled;

                                $shouldValidateOTP = $userIsEnabling || $userIsDisabling;

                                if ($shouldValidateOTP) {
                                    $valid = (new Google2FA)->verifyKey(Crypt::decrypt($user->google_2fa_secret), $value ?? '');

                                    if (!$valid) {
                                        $fail('Your one time password is invalid.');
                                    }
                                }
                            }
                        },
                    ];
                }
            default:break;
        }

        return [];

    }

}
