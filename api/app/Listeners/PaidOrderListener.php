<?php

namespace App\Listeners;

use App\Events\PaidOrderEvent;
use App\Jobs\CreateTicketForOrderJob;

class PaidOrderListener
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
     * @param  \App\Events\CheckUserTokenEvent  $event
     * @return void
     */
    public function handle(PaidOrderEvent $event)
    {
        $job = (new CreateTicketForOrderJob($event->order))->onConnection(config('queue.default'))->onQueue('schedules'); 
        dispatch($job);
    }
}
