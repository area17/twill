<?php

namespace A17\Twill\Models\Behaviors;

use A17\Twill\Models\UserOauth;

trait HasOauth
{

    public function providers()
    {

        return $this->hasMany(UserOauth::class, 'user_id');

    }

    public function linkProvider($oauthUser, $provider)
    {
        $provider = new UserOauth([
            'token'    => $oauthUser->token,
            'provider' => $provider,
            'oauth_id' => $oauthUser->id,
        ]);

        return $this->providers()->save($provider);
    }

}
