<?php

namespace A17\Twill\Models\Behaviors;

use A17\Twill\Models\UserOauth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Socialite\Contracts\User;

trait HasOauth
{
    /**
     * @return HasMany
     */
    public function providers()
    {

        return $this->hasMany(UserOauth::class, 'user_id');
    }

    /**
     * @param  User  $oauthUser
     * @param  string  $provider  Socialite provider
     * @return Model|false
     */
    public function linkProvider($oauthUser, $provider)
    {

        $provider = new UserOauth([
            'token' => $oauthUser->token,
            'avatar' => $oauthUser->avatar,
            'provider' => $provider,
            'oauth_id' => $oauthUser->id,
        ]);

        return $this->providers()->save($provider);
    }
}
