<?php
namespace App\Services\Firebase;

use App\Models\TicketHistory;
use Illuminate\Support\Facades\Log;

class FirebaseService
{
    public function checkTicket ($ticket_id)
    {
        try {
            $ref = 'ticket';
            $ticket = TicketHistory::where('ticket_id', $ticket_id)->first();
            if ($ticket) {
                $database = app('firebase.database');
                $firebase = [
                    'id' => $ticket->ticket_id,
                    'fullname' => $ticket->fullname,
                    'mobile' => $ticket->mobile,
                    'estimate_flight_code' => $ticket->estimate_flight_code,
                    'actual_airport_id' => $ticket->actual_airport_id,
                    'actual_airport_code' => $ticket->airport->code,
                    'updated_at' => date_format($ticket->updated_at, 'Y-m-d H:i:s')
                ];
                $key = $database->getReference($ref)->push($firebase)->getKey();
                $refKey = $ref.'/'.$key;
                $database->getReference($refKey)->update(['key' => $key]);
            }
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }
    }
}
