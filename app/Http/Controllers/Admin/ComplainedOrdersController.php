<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Order;
use App\User;

class ComplainedOrdersController extends Controller
{
    public function index()
    {
        $orders = Order::orderBy('id', 'desc');
        $orders = $orders->whereHas('complain');
        $orders = $orders->paginate(15);
        return view('backend.orders.complain_orders',[
            'orders' => $orders,
        ]);
    }

    public function show($id)
    {
        $order = Order::findOrFail(decrypt($id));
        $order_shipping_address = json_decode($order->shipping_address);
        $delivery_boys = User::where('city', $order_shipping_address->city)
            ->where('user_type', 'delivery_boy')
            ->get();



        return view('backend.orders.complain_orders_detail',[
            'order' => $order,
            'order_shipping_address' => $order_shipping_address,
            'delivery_boys' => $delivery_boys
        ]);
    }
}