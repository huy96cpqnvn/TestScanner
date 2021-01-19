<?php

namespace App\Jobs;

use App\Mail\RequestResetPasswordUserMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class SendMailResetPasswordJob extends Job
{
    protected $user;
    protected $new_password;
    protected $locale;
    
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, String $new_password)
    {
        $this->user = $user;
        $this->new_password = $new_password;
        $this->locale = app('translator')->getLocale();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $mail = new RequestResetPasswordUserMail($this->user, $this->new_password, $this->locale);
        Mail::send($mail);
    }
}
