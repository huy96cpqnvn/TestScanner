<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\Transactions\OrderTransaction;

class UpdateStatusSuccessOrderJob extends Job
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
        (new OrderTransaction)->updateStatusSuccess([
            'order_id' => $this->order->id,
            'updated_by' => 0,
        ], true);
    }
}
