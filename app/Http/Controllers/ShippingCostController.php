<?php

namespace App\Http\Controllers;

use App\Services\RajaOngkirService;
use Illuminate\Http\Request;

class ShippingCostController extends Controller
{
    public function index()
    {
        return view('frontend.shipping_cost_index');
    }

    public function ajaxGetProvinces()
    {
        $rajaOngkir = new RajaOngkirService();
        return $rajaOngkir->getProvinces();
    }

    public function ajaxGetCities(Request $request)
    {
        $this->validate($request, [
            'province_id' => 'required'
        ]);

        $rajaOngkir = new RajaOngkirService();
        return $rajaOngkir->getCities($request->province_id);
    }

    public function ajaxGetCost(Request $request)
    {
        $from_text = $request->from_text;
        $to_text = $request->to_text;
        $from_id = $request->from_id;
        $to_id = $request->to_id;
        $weight = $request->weight;
        $courier = $request->courier;

        $parseData = [
            'from_text' => $from_text,
            'to_text' => $to_text,
            'from_id' => $from_id,
            'to_id' => $to_id,
            'weight' => $weight,
            'courier' => $courier,
        ];

        $rajaOngkir = new RajaOngkirService();
        $results = $rajaOngkir->getCost(
            $parseData['from_id'],
            $parseData['to_id'],
            $parseData['weight'],
            $parseData['courier']
        );

        $parseData['results'] = $results['rajaongkir']['results'];

        return view('frontend.shipping_cost_detail_cost', $parseData);
    }



}