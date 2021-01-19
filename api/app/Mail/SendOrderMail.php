<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\OrderLine;
use App\Models\Ticket;

class SendOrderMail extends BasicMail
{
    protected $order;
    
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Order $order, String $locale)
    {
        $this->order = $order;
        $this->locale = $locale;
    }

    protected function _toEmail()
    {
        return trim($this->order->receiver_email);
    }

    protected function _toName()
    {
        return trim($this->order->receiver_fullname);
    }
 
    protected function _getTemplateCode()
    {
        return 'SEND_ORDER';
    }

    protected function _getLocale()
    {
        return $this->locale;
    }

    protected function _getData()
    {
        $product = $this->_getProduct();
        return [
            'order_code' => $this->order->code,
            'buyer_fullname' => $this->order->buyer_fullname,
            'buyer_email' => $this->order->buyer_email,
            'buyer_mobile' => $this->order->buyer_mobile,            
            'product_name' => $product['name'],
            'product_quantity' => $product['quantity'],
            'order_lookup_url' => Order::getOrderLookupURL($this->order),
        ];
    }

    private function _getProduct()
    {
        $result = null;
        $order_lines = OrderLine::where('order_id', $this->order->id)->get();
        if ($order_lines) {
            foreach ($order_lines as $order_line) {
                if ($result === null) {
                    $result = [
                        'name' => $order_line->item->name,
                        'quantity' => 0,
                    ];
                }
                $result['quantity']++;
                
            }
        }
        return $result;
    }
}