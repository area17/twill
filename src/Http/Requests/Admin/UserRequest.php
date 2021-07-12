<?php

namespace A17\Twill\Http\Requests\Admin;

use Illuminate\Support\Facades\Auth;
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
                    $roleKeyValue = config('twill.enabled.permissions-management') ?
                        ['role_id' => 'required'] :
                        ['role' => 'required|not_in:SUPERADMIN'];

                    return [
                        'name' => 'required',
                        'email' => 'required|email|unique:' . config('twill.users_table', 'twill_users') . ',email',
                    ] + $roleKeyValue;
                }
            case 'PUT':
                {
                    $roleKeyValue = config('twill.enabled.permissions-management') ?
                        [] : ['role' => 'not_in:SUPERADMIN'];

                    return [
                        'name' => 'required',
                        'email' => 'required|email|unique:' . config('twill.users_table', 'twill_users') . ',email,' . $this->route('user'),
                        'verify-code' => function ($attribute, $value, $fail) {
                            $user = Auth::guard('twill_users')->user();
                            $with2faSettings = config('twill.enabled.users-2fa') && $user->id == $this->route('user');

                            if ($with2faSettings) {
                                $userIsEnabling = $this->get('google_2fa_enabled') && !$user->google_2fa_enabled;
                                $userIsDisabling = !$this->get('google_2fa_enabled') && $user->google_2fa_enabled;

                                $shouldValidateOTP = $userIsEnabling || $userIsDisabling;

                                if ($shouldValidateOTP) {
                                    $valid = (new Google2FA)->verifyKey($user->google_2fa_secret, $value ?? '');

                                    if (!$valid) {
                                        $fail('Your one time password is invalid.');
                                    }
                                }
                            }
                        },
                    ] + $roleKeyValue;
                }
            default:break;
        }

        return [];

    }

}
