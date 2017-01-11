<?php

namespace A17\CmsToolkit\Http\Requests\Admin;

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
                        'email' => 'required|email|unique:users,email',
                        'role' => 'required',
                    ];
                }
            case 'PATCH':
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
