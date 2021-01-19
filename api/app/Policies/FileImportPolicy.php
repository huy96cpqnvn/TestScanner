<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserGroup;

class FileImportPolicy extends Policy
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
     * Determine whether the user can create users.
     *
     * @param User  $user
     * @param array $post
     * @return boolean
     */
    public function create(User $user, $post)
    {
        return true;
    }

    /**
     * Determine whether the user can update the user.
     *
     * @param  User  $user
     * @param  array  $post
     * @return boolean
     */
    public function update(User $user, $post)
    {
        return true;
    }

    /**
     * Determine whether the user can update the user.
     *
     * @param  User  $user
     * @param  array  $post
     * @return boolean
     */
    public function closed(User $user, $post)
    {
        return true;
    }
}
