<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy extends Policy
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
        $model = User::isAllowUpdate()->where('id', $params['user_id'])->first();
        if ($model) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can create users.
     *
     * @param  User  $user
     * @return boolean
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the user.
     *
     * @param  User  $user
     * @param  $params
     * @return boolean
     */
    public function updateRole(User $user,$params)
    {
        return $this->view($user, $params);
    }

    /**
     * Determine whether the user can delete the user.
     *
     * @param  User  $user
     * @param  User  $post
     * @return boolean
     */
    public function delete(User $user, User $post)
    {
        //
    }

    /**
     * Determine whether the user can update profile the user.
     *
     * @param  User  $user
     * @param  $post
     * @return boolean
     */
    public function profileUpdate(User $user, $post)
    {
        if ($post['user_id'] == $user->id) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can change password profile the user.
     *
     * @param  User  $user
     * @param  $post
     * @return boolean
     */
    public function profileChangePassword(User $user, $post)
    {
        if ($post['user_id'] == $user->id) {
            return true;
        }
        return false;
    }
}
