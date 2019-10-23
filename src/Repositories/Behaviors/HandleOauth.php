<?php

namespace A17\Twill\Repositories\Behaviors;

trait HandleOauth
{

    /**
     * @param $user
     * @return boolean
     */
    public function oauthUserExists($user)
    {
        return $this->model->whereEmail($user->email)->exists();
    }

    /**
     * @param $user
     * @return boolean
     */
    public function oauthUserLinked($user)
    {
        return true;
    }

    /**
     * @param $user
     * @return boolean
     */
    public function oauthUpdateProvider($user)
    {
        return true;
    }

    /**
     * @param $user
     * @return boolean
     */
    public function oauthCreateUser($user)
    {
        // Create the user, link it with it's social profile)
        return true;
    }

}
