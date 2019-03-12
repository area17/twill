<?php

namespace Sb4yd3e\Twill\Http\Requests\Admin;

class MediaRequest extends Request
{
    public function rules()
    {
        return config('twill.media_library.endpoint_type') === 'local'
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
