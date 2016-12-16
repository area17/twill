<?php

namespace A17\CmsToolkit\Http\Requests\Admin;

class MediaRequest extends Request
{
    public function rules()
    {
        return config('cms-toolkit.media_library.endpoint_type') === 'local'
        ? [
            'qqfilename' => 'required',
            'qqfile' => 'required',
        ]
        : [
            'key' => 'required',
            'name' => 'required',
        ];
    }
}
