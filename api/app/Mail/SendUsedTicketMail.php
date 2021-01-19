<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\Ticket;
use Illuminate\Support\Facades\Storage;

class SendUsedTicketMail extends BasicMail
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
        return 'SEND_USED_TICKET';
    }

    protected function _getLocale()
    {
        return $this->locale;
    }

    protected function _getData()
    {
        return [
            'ticket_code' => $this->ticket->code,
            'ticket_used_at' => $this->ticket->used_at,
            'actual_airport_name' => $this->_getActualAirportName(),
        ];
    }

    private function _getActualAirportName()
    {
        return 'Sân bay ...';
    }

    public function build()
    {        
        parent::build();
        $this->attachFromStorageDisk('local', $this->ticket->file_path, 'Ticket_'.$this->ticket->code.'.pdf');
    }
}