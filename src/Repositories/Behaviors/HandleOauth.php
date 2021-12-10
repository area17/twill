<?php

namespace A17\Twill\Repositories\Behaviors;

trait HandleOauth
{

    /**
     * @param \Laravel\Socialite\Contracts\User $oauthUser
     * @return \A17\Twill\Models\User
     */
    public function oauthUser($oauthUser)
    {
        return $this->model->whereEmail($oauthUser->email)->first();
    }

    /**
     * @param \Laravel\Socialite\Contracts\User $oauthUser
     * @param string $provider
     * @return boolean
     */
    public function oauthIsUserLinked($oauthUser, $provider)
    {
        $user = $this->model->whereEmail($oauthUser->email)->first();

        return $user->providers()
            ->where(['provider' => $provider, 'oauth_id' => $oauthUser->id])
            ->exists();
    }

    /**
     * @param \Laravel\Socialite\Contracts\User $oauthUser
     * @param string $provider
     * @return \A17\Twill\Models\User
     */
    public function oauthUpdateProvider($oauthUser, $provider)
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
     * @param \Laravel\Socialite\Contracts\User $oauthUser
     * @return \A17\Twill\Models\User
     */
    public function oauthCreateUser($oauthUser)
    {

        $user = $this->model->firstOrNew([
            'name' => $oauthUser->name,
            'email' => $oauthUser->email,
            'role' => config('twill.oauth.default_role'),
            'published' => true,
        ]);

        $user->save();

        return $user;

    }

}
