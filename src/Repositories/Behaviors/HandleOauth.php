<?php

namespace A17\Twill\Repositories\Behaviors;

trait HandleOauth
{

    /**
     * @param $oauthUser
     * @return A17\Twill\Models\User
     */
    public function oauthUser($oauthUser)
    {
        return $this->model->whereEmail($oauthUser->email)->first();
    }

    /**
     * @param $oauthUser
     * @param $provider
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
     * @param $oauthUser
     * @param $provider
     * @return A17\Twill\Models\User
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
     * @param $oauthUser
     * @return A17\Twill\Models\User
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
