<?php

namespace A17\Twill\Http\Requests\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use PragmaRX\Google2FA\Google2FA;

class UserRequest extends Request
{
    /**
     * Determines if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Gets the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch ($this->method()) {
            case 'POST':
                {
                    return [
                        'name' => 'required',
                        'email' => 'required|email|unique:' . config('twill.users_table', 'twill_users') . ',email',
                        'role' => 'required|not_in:SUPERADMIN',
                    ];
                }
            case 'PUT':
                {
                    return [
                        'name' => 'required',
                        'role' => 'not_in:SUPERADMIN',
                        'email' => 'required|email|unique:' . config('twill.users_table', 'twill_users') . ',email,' . $this->get('user'),
                        'verify-code' => function ($attribute, $value, $fail) {
                            $user = Auth::guard('twill_users')->user();
                            $with2faSettings = config('twill.enabled.users-2fa') && $user->id == $this->get('user');

                            if ($with2faSettings) {
                                $userIsEnabling = $this->get('google_2fa_enabled') && !$user->google_2fa_enabled;
                                $userIsDisabling = !$this->get('google_2fa_enabled') && $user->google_2fa_enabled;

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
