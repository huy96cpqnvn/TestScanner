<?php

namespace App\Listeners;

use App\Events\UserLoginSuccessEvent;
use App\Models\UserLoginHistory;
use App\Services\Transactions\UserLoginHistoryTransaction;

class UserLoginSuccessListener
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
    public function handle(UserLoginSuccessEvent $event)
    {
        (new UserLoginHistoryTransaction)->create([
            'user_id' => $event->user->id,
            'ip' => getIP(),
            'status' => UserLoginHistory::STATUS_SUCCESS,
        ], true);
    }
}
