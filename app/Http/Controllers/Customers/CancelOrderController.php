<?php

namespace App\Http\Controllers\Customers;

use App\Http\Controllers\Controller;
use App\Order;
use App\OrderDetail;

class CancelOrderController extends Controller
{
    public function modal($order_code)
    {
        $order = Order::where('code', $order_code)->firstOrFail();
        return view('frontend.user.customer.orders.cancel_order_modal',[
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
        
        wallet_proccess($order->user_id, $order->grand_total, 'Order Canceled', 'Order Canceled - #'.$order->code, 'TOPUP');
        // wallet_proccess($order->seller_id, $order->grand_total*-1, 'Order Canceled', 'Order Canceled - #'.$order->code, 'DISBURSEMENT');


        return redirect()->route('purchase_history.index');
    }
}