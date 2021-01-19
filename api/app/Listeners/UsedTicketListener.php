<?php

namespace App\Listeners;

use App\Events\UsedTicketEvent;
use App\Jobs\SendMailUsedTicketJob;
use App\Services\Firebase\FirebaseService;

class UsedTicketListener
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
    public function handle(UsedTicketEvent $event)
    {
        $ticket_id = $event->ticket->id;
        (new FirebaseService())->checkTicket($ticket_id);

        $job = (new SendMailUsedTicketJob($event->ticket))->allOnConnection(config('queue.default'))->allOnQueue('notifications');
        dispatch($job);
    }
}
