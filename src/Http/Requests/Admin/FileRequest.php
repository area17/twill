<?php

namespace A17\CmsToolkit\Http\Requests\Admin;

class FileRequest extends Request
{
    public function rules()
    {
        return config('cms-toolkit.file_library.endpoint_type') === 'local'
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
