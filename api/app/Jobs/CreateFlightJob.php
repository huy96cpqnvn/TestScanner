<?php

namespace App\Jobs;

use App\Models\Flight;
use App\Services\Transactions\FlightTransaction;

class CreateFlightJob extends Job
{
    protected $flight;

      /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($flight)
    {
        $this->flight = $flight;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {  
        $transaction = new FlightTransaction(); 
        $transaction->create($this->flight, true);
    }
}