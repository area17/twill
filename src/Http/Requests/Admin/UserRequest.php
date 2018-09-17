<?php

namespace A17\Twill\Http\Requests\Admin;

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
                    ];
                }
            default:break;
        }

        return [];

    }

}
