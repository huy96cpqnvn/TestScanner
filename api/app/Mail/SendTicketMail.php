<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\Ticket;
use Illuminate\Support\Facades\Storage;

class SendTicketMail extends BasicMail
{
    protected $ticket;
    
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Ticket $ticket, String $locale)
    {
        $this->ticket = $ticket;
        $this->locale = $locale;
    }

    protected function _toEmail()
    {
        return trim($this->ticket->ticket_history->email);
    }

    protected function _toName()
    {
        return trim($this->ticket->ticket_history->fullname);
    }
 
    protected function _getTemplateCode()
    {
        return 'SEND_TICKET';
    }

    protected function _getLocale()
    {
        return $this->locale;
    }

    protected function _getData()
    {
        return [
            'ticket' => $this->ticket            
        ];
    }

    public function build()
    {        
        parent::build();
        $this->attachFromStorageDisk('local', $this->ticket->file_path, 'Ticket_'.$this->ticket->code.'.pdf');
    }
}