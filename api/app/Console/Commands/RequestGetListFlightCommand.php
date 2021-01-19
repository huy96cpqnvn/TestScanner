<?php

namespace App\Console\Commands;

use App\Models\Airport;
use App\Jobs\CreateFlightJob;
use Illuminate\Console\Command;
use App\Services\AviationstackFlight;

class RequestGetListFlightCommand extends Command
{
    protected $name = 'command:RequestGetListFlightCommand';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    private function _getListAirPorts()
    {
        $list_airports = Airport::where('status', Airport::STATUS_ACTIVE)->get();
        return $list_airports;    
    }

    /**
     *
     * @return mixed
     */
    public function handle()
    {
        $list_airports = $this->_getListAirPorts();
        if ($list_airports == []) {
            $this->error('Không tồn tại sân bay');
        } else {
            foreach ($list_airports as $airport) {
                $dep_iata = $airport->code;
                $offset = 0;
                do {
                    $result = (new AviationstackFlight)->getFlights($dep_iata, $offset);
                    if (!empty($result['data'])) {
                        foreach ($result['data'] as $flight) {
                            // dd($flight);
                            $job = new CreateFlightJob($flight);
                            $job->onConnection('sync')->onQueue('schedules');
                            dispatch($job);
                        }
                    }
                    $offset = $result['offset'];
                } while ($result['next'] == true);
            }
        }
    }
}