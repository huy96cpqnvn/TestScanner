<?php

namespace App\Jobs;

use App\Mail\RequestSendMailJob;
use App\Models\RequestSendMail;
use Illuminate\Support\Facades\Mail;

class ProcessRequestSendMailJob extends Job
{
    protected $requestSendMail;
    protected $to_email;
    protected $files;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(RequestSendMail $requestSendMail, String $to_email, $files)
    {
        $this->requestSendMail = $requestSendMail;
        $this->to_email = $to_email;
        $this->files = $files;
    }

    public function handle()
    {
        Mail::to($this->to_email)
            ->send(new RequestSendMailJob($this->requestSendMail, $this->files));
    }
}
