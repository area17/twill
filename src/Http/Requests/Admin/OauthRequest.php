<?php

namespace A17\Twill\Http\Requests\Admin;

use Illuminate\Validation\Rule;

class OauthRequest extends Request
{

    /**
     * @var string
     */
    protected $redirectRoute = 'twill.login.form';


    /**
     * Include route parameters for validation
     *
     * @return mixed[]
     */
    public function all($keys = null): array
    {

        $data = parent::all();
        $data['provider'] = $this->input('provider', $this->route('provider'));

        return $data;

    }

    /**
     * Gets the validation rules that apply to the request.
     *
     * @return array<string, array<string|\Illuminate\Validation\Rules\In>>
     */
    public function rules(): array
    {

        return [
            'provider' => [
                'required',
                Rule::in(config('twill.oauth.providers', []))
            ],
        ];

    }

}
