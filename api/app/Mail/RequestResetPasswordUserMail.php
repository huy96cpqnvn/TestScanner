<?php

namespace App\Mail;

use App\Models\User;

class RequestResetPasswordUserMail extends BasicMail
{
    protected $user;
    protected $new_password;
    
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, String $new_password, String $locale)
    {
        $this->user = $user;
        $this->new_password = $new_password;
        $this->locale = $locale;
    }

    protected function _toEmail()
    {
        return trim($this->user->email);
    }

    protected function _toName()
    {
        return trim($this->user->fullname);
    }
 
    protected function _getTemplateCode()
    {
        return 'REQUEST_RESET_PASSWORD_USER';
    }

    protected function _getLocale()
    {
        return $this->locale;
    }

    protected function _getData()
    {
        return [
            'fullname' => $this->user->fullname,            
            'new_password' => $this->new_password,
        ];
    }
}