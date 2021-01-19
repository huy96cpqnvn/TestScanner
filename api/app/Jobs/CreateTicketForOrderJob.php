<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\Transactions\TicketTransaction;
use Exception;

class CreateTicketForOrderJob extends Job
{
    protected $order;    
    protected $locale;
    
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;        
        $this->locale = app('translator')->getLocale();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // throw new Exception('ok');
        (new TicketTransaction)->createByOrder([
            'order_id' => $this->order->id, 
            'created_by' => 0
        ], true);
    }
}
