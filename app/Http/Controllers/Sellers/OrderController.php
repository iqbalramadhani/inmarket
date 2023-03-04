<?php

namespace App\Http\Controllers\Sellers;

use App\Http\Controllers\Controller;
use App\Models\Courier;
use App\Models\Order;
use App\Models\Complain;
use App\Models\OrderDetail;
use Auth;
use Illuminate\Http\Request;
use App\Utility\NotificationUtility;

class OrderController extends Controller
{
    public function detail($id)
    {
        $order = Order::with('orderDetails')->findOrFail(decrypt($id));
        return view('frontend.user.seller.order.detail', compact('order'));
    }

    public function updateDeliverStatus($id)
    {
        $order = Order::findOrFail(decrypt($id));
        $couriers = Courier::all();
        $shop = Auth::user()->shop;
        return view('frontend.user.seller.order.update_deliver_status', [
            'order' => $order,
            'couriers'  => $couriers,
            'shop' => $shop
        ]);
    }

    public function updateDeliverStatusAction(Request $request, $id): \Illuminate\Http\RedirectResponse
    {
        $order = Order::findOrFail(decrypt($id));
        $order->resi_number = $request->resi;
        $order->resi_courier = $request->resi_courier;
        $order->resi_courier_service = $request->resi_courier_service;
        $order->delivery_status = 'on_delivery';
        $order->save();
        $notif = NotificationUtility::sendNotification($order, 'sent');
        OrderDetail::where('order_id', $order->id)->update([
            'delivery_status' => 'on_delivery'
        ]);

        return redirect()->route('orders.index');
    }
}