<?php

namespace App\Jobs;

use App\Mail\SendTicketMail;
use App\Models\Ticket;
use Exception;
use Illuminate\Support\Facades\Mail;

class SendMailTicketJob extends Job
{
    protected $ticket;
    protected $locale;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
        $this->locale = app('translator')->getLocale();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $mail = new SendTicketMail($this->ticket, $this->locale);
        Mail::send($mail);
        // throw new Exception('1');
    }
}
