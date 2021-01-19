<?php

namespace App\Http\Middleware;


class AppAuthenticate extends Authenticate
{
    /**
     * @param \App\Models\User $user
     * @param string $current_action
     * @return boolean
     */
    protected function _checkRoleApi(\App\Models\User $user, $current_action)
    {
        if (parent::_checkRoleApi($user, $current_action)) {
            $user_group = $user->getUserGroupById($user->current_user_group_id);
            if ($user_group->role->code == 'AIRPORT') {
                return true;
            }
        }
        return false;
    }
}
