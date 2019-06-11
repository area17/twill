<?php

namespace A17\Twill\Http\Requests\Admin;

class FileRequest extends Request
{
    /**
     * Gets the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return config('twill.file_library.endpoint_type') === 'local'
        ? [
            'qqfilename' => 'required',
            'qqfile' => 'required',
            'qqtotalfilesize' => 'required',
        ]
        : [
            'key' => 'required',
            'name' => 'required',
        ];
    }
}
