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
            : config('twill.media_library.endpoint_type') === 'azure'
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
