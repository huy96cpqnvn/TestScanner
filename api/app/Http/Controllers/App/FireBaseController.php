<?php
namespace App\Http\Controllers\App;
use Carbon\Carbon;

class FireBaseController extends AppApiController
{
    public function test ()
    {
        $ref = 'ticket';
        for ($i = 1; $i <= 20; $i++) {
            $database = app('firebase.database');
            $firebase = [
                'id' => $i,
                'fullname' => "Dev Grooo " . $i,
                'mobile' => '0123456789',
                'estimate_flight_code' => "123".$i,
                'actual_airport_id' => 1,
                'actual_airport_code' => 'NBH',
                'updated_at' => date_format(Carbon::now(), 'Y-m-d H:i:s'),
            ];
            $key = $database->getReference($ref)->push($firebase)->getKey();
            $refKey = $ref.'/'.$key;
            $database->getReference($refKey)->update(['key' => $key]);
        }
    }
}
