<?php

namespace App\Http\Controllers\Sellers;

use App\Http\Controllers\Controller;
use App\Models\RajaOngkirCity;
use App\Models\RajaOngkirSubdistrict;
use App\Models\RajaOngkirProvince;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function ajaxGetProvinces()
    {
        $provinces = RajaOngkirProvince::orderBy('province_name', 'asc')->get();
        return response()->json($provinces);
    }

    public function ajaxGetCities(Request $request)
    {
        $this->validate($request, [
            'province_id' => 'required'
        ]);

        $cities = RajaOngkirCity::where('province_id', $request->get('province_id'))->get();
        return response()->json($cities);
    }

    public function ajaxGetSubdistricts(Request $request)
    {
        $this->validate($request, [
            'city_id' => 'required'
        ]);

        $subdistricts = RajaOngkirSubdistrict::where('city_id', $request->get('city_id'))->get();
        return response()->json($subdistricts);
    }
}