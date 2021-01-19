<?php

namespace App\Events;

use App\Models\Order;

class PaidOrderEvent extends Event
{
    public $order;
    
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }
}
