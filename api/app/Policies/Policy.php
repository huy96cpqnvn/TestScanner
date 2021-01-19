<?php

namespace App\Policies;

use App\Models\User;

class Policy
{
    /**
     * Determine whether the user can view the user.
     *
     * @param  User  $user
     * @param  array  $params
     * @return boolean
     */
    public function view(User $user, array $params)
    {
        return true;
    }

    /**
     * Determine whether the user can update the user.
     *
     * @param  User  $user
     * @param  array  $params
     * @return boolean
     */
    public function update(User $user, $params)
    {
        return $this->view($user, $params);
    }
}
