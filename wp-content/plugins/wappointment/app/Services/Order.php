<?php

namespace Wappointment\Services;

use Wappointment\Helpers\Translations;
use Wappointment\Models\Order as ModelOrder;
class Order
{
    public $order = null;
    public function __construct($transaction_id)
    {
        $this->order = \Wappointment\Models\Order::where('transaction_id', $transaction_id)->firstOrFail();
    }
    /**
     * Undocumented function
     *
     * @return void
     */
    public function awaitPayment()
    {
        if ($this->order->isOnSite() && $this->order->isPending()) {
            $this->order->setAwaitingPayment();
            $this->order->save();
        } else {
            throw new \WappointmentException(\Wappointment\Helpers\Translations::get('forbidden'), 1);
        }
    }
    public static function refund($order_id)
    {
        $order = \Wappointment\Models\Order::findOrFail($order_id);
        $order->refund();
    }
    public static function markPaid($order_id, $purchase_info = '')
    {
        $order = \Wappointment\Models\Order::findOrFail($order_id);
        if (!empty($purchase_info)) {
            $options = $order->options;
            $options['purchase_info'] = $purchase_info;
            $order->options = $options;
        }
        $order->complete(false);
        $order->save();
    }
    public static function cancel($order_id, $cancel_info = '')
    {
        $order = \Wappointment\Models\Order::findOrFail($order_id);
        $order->setCancelled();
        if (!empty($cancel_info)) {
            $options = $order->options;
            $options['cancel_info'] = $cancel_info;
            $order->options = $options;
        }
        $order->save();
    }
}
