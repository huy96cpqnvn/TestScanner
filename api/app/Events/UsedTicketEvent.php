<?php

namespace App\Events;

use App\Models\Ticket;

class UsedTicketEvent extends Event
{
    public $ticket;
    
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }
}
