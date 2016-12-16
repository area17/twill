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
        return [
            'name' => 'required',
            // 'email' => 'required|email|max:255',
        ];
    }

}
