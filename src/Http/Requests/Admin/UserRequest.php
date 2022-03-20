<?php

namespace A17\Twill\Http\Requests\Admin;

use A17\Twill\Models\User;
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
                        'force-2fa-disable-challenge' => function ($attribute, $value, $fail) {
                            $user = User::findOrFail($this->route('user'));
                            if ($this->get('google_2fa_enabled') || !$user->google_2fa_enabled) {
                                return;
                            }

                            $loggedInAdmin = Auth::guard('twill_users')->user();
                            if (!$loggedInAdmin->can('manage-users')) {
                                return $fail('Unprivileged action');
                            }

                            if (!$loggedInAdmin->google_2fa_enabled) {
                                return $fail('You must have 2FA enabled to do this action');
                            }

                            $challenge = twillTrans('twill::lang.user-management.force-2fa-disable-challenge', ['user' => $user->email]);
                            if ($value !== $challenge) {
                                return $fail('Challenge mismatch');
                            }
                        },
                    ];
                }
            default:break;
        }

        return [];

    }

}
