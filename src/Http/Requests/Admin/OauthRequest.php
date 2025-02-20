<?php

namespace A17\Twill\Http\Requests\Admin;

use Illuminate\Validation\Rule;

class OauthRequest extends Request
{
    /**
     * Include route parameters for validation
     *
     * @return array
     */
    public function all($keys = null)
    {

        $data = parent::all();
        $data['provider'] = $this->input('provider', $this->route('provider'));

        return $data;
    }

    /**
     * Gets the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        return [
            'provider' => [
                'required',
                Rule::in(config('twill.oauth.providers', [])),
            ],
        ];
    }

    protected function getRedirectUrl()
    {
        $url = $this->redirector->getUrlGenerator();

        return $url->route(config('twill.admin_route_name_prefix') . 'login.form');
    }
}
