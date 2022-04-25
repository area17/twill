<?php

namespace A17\Twill\Http\Requests\Admin;

class FileRequest extends Request
{
    /**
     * Gets the validation rules that apply to the request.
     *
     * @return mixed[]
     */
    public function rules(): array
    {
        return match (config('twill.file_library.endpoint_type')) {
            'local' => [
                'qqfilename' => 'required',
                'qqfile' => 'required',
                'qqtotalfilesize' => 'required',
            ],
            'azure' => [
                'blob' => 'required',
                'name' => 'required',
            ],
            default => [
                'key' => 'required',
                'name' => 'required',
            ],
        };
    }
}
