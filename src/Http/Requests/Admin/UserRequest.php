<?php

namespace A17\Twill\Http\Requests\Admin;

use A17\Twill\Models\Role;
use A17\Twill\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
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
     */
    public function rules(): array
    {
        switch ($this->method()) {
            case 'POST':
                return [
                        'name' => 'required',
                        'email' => 'required|email|unique:' . config('twill.users_table', 'twill_users') . ',email',
                    ] + $this->getRoleValidator(['required']);
            case 'PUT':
                return [
                        'name' => 'required',
                        'email' => 'required|email|unique:' . config(
                            'twill.users_table',
                            'twill_users'
                        ) . ',email,' . $this->route('user'),
                        'verify-code' => function ($attribute, $value, $fail) {
                            $user = Auth::guard('twill_users')->user();
                            $with2faSettings = config('twill.enabled.users-2fa') && $user->id == $this->route('user');

                            if ($with2faSettings) {
                                $userIsEnabling = $this->get('google_2fa_enabled') && ! $user->google_2fa_enabled;
                                $userIsDisabling = ! $this->get('google_2fa_enabled') && $user->google_2fa_enabled;

                                $shouldValidateOTP = $userIsEnabling || $userIsDisabling;

                                if ($shouldValidateOTP) {
                                    $valid = (new Google2FA())->verifyKey($user->google_2fa_secret, $value ?? '');

                                    if (! $valid) {
                                        $fail('Your one time password is invalid.');
                                    }
                                }
                            }
                        },
                        'force-2fa-disable-challenge' => function ($attribute, $value, $fail) {
                            $user = User::findOrFail($this->route('user'));
                            if ($this->get('google_2fa_enabled') || ! $user->google_2fa_enabled) {
                                return;
                            }

                            $loggedInAdmin = Auth::guard('twill_users')->user();
                            if (! $loggedInAdmin->can('manage-users')) {
                                return $fail('Unprivileged action');
                            }

                            if (! $loggedInAdmin->google_2fa_enabled) {
                                return $fail('You must have 2FA enabled to do this action');
                            }

                            $challenge = twillTrans(
                                'twill::lang.user-management.force-2fa-disable-challenge',
                                ['user' => $user->email]
                            );
                            if ($value !== $challenge) {
                                return $fail('Challenge mismatch');
                            }
                        },
                    ] + $this->getRoleValidator();
            default:
                break;
        }

        return [];
    }

    /**
     * @return array
     */
    private function getRoleValidator($baseRule = [])
    {
        if (config('twill.enabled.permissions-management')) {
            // Users can't assign roles above their own
            $accessibleRoleIds = Role::accessible()->pluck('id')->toArray();
            $baseRule[] = Rule::in($accessibleRoleIds);
        } else {
            $baseRule[] = 'not_in:SUPERADMIN';
        }

        return [User::getRoleColumnName() => $baseRule];
    }
}
