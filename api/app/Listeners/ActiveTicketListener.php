<?php

namespace App\Listeners;

use App\Events\ActiveTicketEvent;
use App\Jobs\CreateTicketForOrderJob;
use App\Jobs\SendMailTicketJob;

class ActiveTicketListener
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
    public function handle(ActiveTicketEvent $event)
    {
        $job = (new SendMailTicketJob($event->ticket))->allOnConnection(config('queue.default'))->allOnQueue('notifications'); 
        dispatch($job);
    }
}
