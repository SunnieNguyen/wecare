<?php

namespace Wappointment\Controllers;

use Wappointment\ClassConnect\Request;
use Wappointment\Models\Order as OrderModel;
use Wappointment\Services\Settings;
use Wappointment\Services\Order as ServicesOrder;
class OrdersBackController extends \Wappointment\Controllers\RestController
{
    public function index(\Wappointment\ClassConnect\Request $request)
    {
        if (!empty($request->input('per_page'))) {
            \Wappointment\Services\Settings::saveStaff('per_page', $request->input('per_page'));
        }
        return ['page' => $request->input('page'), 'viewData' => ['per_page' => \Wappointment\Services\Settings::getStaff('per_page'), 'tax' => \Wappointment\Services\Settings::get('tax')], 'orders' => $this->getOrders()];
    }
    protected function getOrders()
    {
        $query = \Wappointment\Models\Order::orderBy('id', 'DESC');
        return $query->paginate(\Wappointment\Services\Settings::getStaff('per_page'));
    }
    public function refund(\Wappointment\ClassConnect\Request $request)
    {
        \Wappointment\Services\Order::refund($request->input('order_id'));
        return ['message' => __('Order has been refunded', 'wappointment')];
    }
    public function markAsPaid(\Wappointment\ClassConnect\Request $request)
    {
        \Wappointment\Services\Order::markPaid($request->input('order_id'), $request->input('purchase_info'));
        return ['message' => __('Order has been paid', 'wappointment')];
    }
    public function cancel(\Wappointment\ClassConnect\Request $request)
    {
        \Wappointment\Services\Order::cancel($request->input('order_id'), $request->input('cancel_info'));
        return ['message' => __('Order has been cancelled', 'wappointment')];
    }
}
