<?php

namespace App\Jobs;

use App\Mail\CreateUserMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class SendMailCreateUserJob extends Job
{
    protected $user;
    protected $password;
    protected $locale;
    
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, String $password)
    {
        $this->user = $user;
        $this->password = $password;
        $this->locale = app('translator')->getLocale();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $mail = new CreateUserMail($this->user, $this->password, $this->locale);
        Mail::send($mail);
    }
}
