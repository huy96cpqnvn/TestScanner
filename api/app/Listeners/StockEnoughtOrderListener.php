<?php

namespace App\Listeners;

use App\Events\StockEnoughtOrderEvent;
use App\Jobs\SendMailOrderJob;
use App\Jobs\UpdateStatusSuccessOrderJob;
use App\Models\Order;
use App\Models\OrderLine;
use App\Services\Transactions\OrderTransaction;
use App\Services\Transactions\TicketTransaction;

class StockEnoughtOrderListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\CheckUserTokenEvent  $event
     * @return void
     */
    public function handle(StockEnoughtOrderEvent $event)
    {
        if ($this->_isOrderBuyTicketType($event->order, 'E0')) {
            (new TicketTransaction)->activeByOrder([
                'order_id' => $event->order->id,
                'updated_by' => 0,
            ], true);
        } else {
            $job = (new SendMailOrderJob($event->order))->chain([
                new UpdateStatusSuccessOrderJob($event->order)
            ]);
            $job->allOnConnection(config('queue.default'))->allOnQueue('notifications'); 
            dispatch($job);
        }
    }

    private function _isOrderBuyTicketType(Order $order, $type_code)
    {
        $order_lines = OrderLine::where('order_id', $order->id)->get();
        if ($order_lines) {
            foreach ($order_lines as $order_line) {
                if ($order_line->item->ref->code == $type_code) {
                    return true;   
                }
            }
        }
        return false;
    }
}
