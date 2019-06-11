<?php

namespace A17\Twill\Http\Requests\Admin;

class MediaRequest extends Request
{
    /**
     * Gets the validation rules that apply to the request.
     *
     * @return array
     */
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
