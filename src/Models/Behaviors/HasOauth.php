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
     * @param string $provider Socialite provider
     */
    public function linkProvider(\Laravel\Socialite\Contracts\User $oauthUser, string $provider): \Illuminate\Database\Eloquent\Model|false
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
