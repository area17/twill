<?php

namespace A17\Twill\Repositories\Behaviors;

trait HandleOauth
{

    /**
     * @return \A17\Twill\Models\User
     */
    public function oauthUser(\Laravel\Socialite\Contracts\User $oauthUser)
    {
        return $this->model->whereEmail($oauthUser->email)->first();
    }

    /**
     * @return boolean
     */
    public function oauthIsUserLinked(\Laravel\Socialite\Contracts\User $oauthUser, string $provider)
    {
        $user = $this->model->whereEmail($oauthUser->email)->first();

        return $user->providers()
            ->where(['provider' => $provider, 'oauth_id' => $oauthUser->id])
            ->exists();
    }

    /**
     * @return \A17\Twill\Models\User
     */
    public function oauthUpdateProvider(\Laravel\Socialite\Contracts\User $oauthUser, string $provider)
    {
        $user = $this->model->whereEmail($oauthUser->email)->first();
        $provider = $user->providers()
            ->where(['provider' => $provider, 'oauth_id' => $oauthUser->id])
            ->first();

        $provider->token = $oauthUser->token;
        $provider->avatar = $oauthUser->avatar;
        $provider->save();

        return $user;
    }

    /**
     * @return \A17\Twill\Models\User
     */
    public function oauthCreateUser(\Laravel\Socialite\Contracts\User $oauthUser)
    {
        if (config('twill.enabled.permissions-management')) {
            $defaultRole = twillModel('role')::where('name', config('twill.oauth.permissions_default_role'))->first();
            $roleKeyValue = ['role_id' => $defaultRole->id];
        } else {
            $roleKeyValue = ['role' => config('twill.oauth.default_role')];
        }

        $user = $this->model->firstOrNew([
            'name' => $oauthUser->name,
            'email' => $oauthUser->email,
            'published' => true,
        ] + $roleKeyValue);

        $user->save();

        return $user;

    }

}
