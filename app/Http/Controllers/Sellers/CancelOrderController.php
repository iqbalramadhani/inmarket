<?php

namespace App\Http\Controllers\Sellers;

use App\Http\Controllers\Controller;
use App\Order;
use App\OrderDetail;

class CancelOrderController extends Controller
{
    public function modal($order_code)
    {
        $order = Order::where('code', $order_code)->firstOrFail();
        return view('frontend.user.seller.orders.cancel_order_modal',[
            'order' => $order,
        ]);
    }

    public function cancelOrder($order_code): \Illuminate\Http\RedirectResponse
    {
        $order = Order::where('code', $order_code)->firstOrFail();
        $order->delivery_status = 'cancelled';
        $order->save();
        OrderDetail::where('order_id', $order->id)->update([
            'delivery_status' => 'cancelled'
        ]);
        return redirect()->route('orders.index');
    }
}