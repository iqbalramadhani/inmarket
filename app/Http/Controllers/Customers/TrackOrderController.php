<?php

namespace App\Http\Controllers\Customers;

use App\Http\Controllers\Controller;
use App\Order;
use App\Models\Complain;
use App\Services\RajaOngkirService;
use stdClass;

class TrackOrderController extends Controller
{
    public function index($order_code)
    {
        $order = Order::where('code', $order_code)->firstOrFail();
        $rajaongkir = new RajaOngkirService;
        $track = $rajaongkir->track($order->resi_number, $order->resi_courier);
        $track_order = new StdClass();
        $track_order->valid = false;

        if($track['rajaongkir']['status']['code'] === 200){
            $result = $track['rajaongkir']['result'];
            $summary = $track['rajaongkir']['result']['summary'];
            $detail = $track['rajaongkir']['result']['details'];
            $manifest = $track['rajaongkir']['result']['manifest'];
            $track_order->delivered_status = $result['delivered'];
            $track_order->courier_name = $summary['courier_name'];
            $track_order->resi_number = $summary['waybill_number'];
            $track_order->service_code = $summary['service_code'];
            $track_order->shipper_name = $summary['shipper_name'];
            $track_order->receiver_name = $summary['receiver_name'];
            $track_order->status = $summary['status'];
            $track_order->shipper_address1 = $detail['shipper_address1'];
            $track_order->receiver_address1 = $detail['receiver_address1'];
            $track_order->manifest = $manifest;
            $track_order->valid = true;
        }

        return view('frontend.user.customer.orders.track_order_index', [
            'order' => $order,
            'track_order' => $track_order
        ]);
    }
    
    public function track_complain($complain_slug)
    {
        $complain = Complain::where('id', decrypt($complain_slug))->firstOrFail();

        // dd($complain->order_detail()->first()->order->toArray());
        // $order = Order::where('code', $order_code)->firstOrFail();
        $order = $complain->order()->first();
        $rajaongkir = new RajaOngkirService;
        $track = $rajaongkir->track($complain->resi_number, $complain->resi_courier);
        $track_order = new StdClass();
        $track_order->valid = false;

        if($track['rajaongkir']['status']['code'] === 200){
            $result = $track['rajaongkir']['result'];
            $summary = $track['rajaongkir']['result']['summary'];
            $detail = $track['rajaongkir']['result']['details'];
            $manifest = $track['rajaongkir']['result']['manifest'];
            $track_order->delivered_status = $result['delivered'];
            $track_order->courier_name = $summary['courier_name'];
            $track_order->resi_number = $summary['waybill_number'];
            $track_order->service_code = $summary['service_code'];
            $track_order->shipper_name = $summary['shipper_name'];
            $track_order->receiver_name = $summary['receiver_name'];
            $track_order->status = $summary['status'];
            $track_order->shipper_address1 = $detail['shipper_address1'];
            $track_order->receiver_address1 = $detail['receiver_address1'];
            $track_order->manifest = $manifest;
            $track_order->valid = true;
        }

        return view('frontend.user.customer.complains.track_complain', [
            'complain' => $complain,
            'track_order' => $track_order
        ]);
    }
}