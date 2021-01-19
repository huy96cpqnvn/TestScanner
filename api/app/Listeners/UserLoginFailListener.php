<?php

namespace App\Listeners;

use App\Events\UserLoginFailEvent;
use App\Models\UserLoginHistory;
use App\Services\Transactions\UserLoginHistoryTransaction;

class UserLoginFailListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  $event
     * @return void
     */
    public function handle(UserLoginFailEvent $event)
    {
        (new UserLoginHistoryTransaction)->create([
            'user_id' => $event->user->id,
            'ip' => getIP(),
            'status' => UserLoginHistory::STATUS_FAIL,
        ], true);
    }
}
