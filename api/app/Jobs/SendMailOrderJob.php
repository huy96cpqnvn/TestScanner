<?php

namespace App\Jobs;

use App\Mail\SendOrderMail;
use App\Models\Order;
use Exception;
use Illuminate\Support\Facades\Mail;

class SendMailOrderJob extends Job
{
    protected $order;
    protected $locale;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->locale = app('translator')->getLocale();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $mail = new SendOrderMail($this->order, $this->locale);
        Mail::send($mail);
        // throw new Exception('1');
    }
}
