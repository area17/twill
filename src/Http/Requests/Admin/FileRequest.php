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
            : config('twill.file_library.endpoint_type') === 'azure'
                ? [
                    'blob' => 'required',
                    'name' => 'required',
                ]
                : [
                    'key' => 'required',
                    'name' => 'required',
                ];
    }
}
