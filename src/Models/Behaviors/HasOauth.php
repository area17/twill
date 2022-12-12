<?php

namespace A17\Twill\Models\Behaviors;

use A17\Twill\Models\UserOauth;

trait HasOauth
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function providers()
    {

        return $this->hasMany(UserOauth::class, 'user_id');
    }

    /**
     * @param \Laravel\Socialite\Contracts\User $oauthUser
     * @param string $provider Socialite provider
     * @return \Illuminate\Database\Eloquent\Model|false
     */
    public function linkProvider($oauthUser, $provider)
    {

        $provider = new UserOauth([
            'token'    => $oauthUser->token,
            'avatar'   => $oauthUser->avatar,
            'provider' => $provider,
            'oauth_id' => $oauthUser->id,
        ]);

        return $this->providers()->save($provider);
    }
}
