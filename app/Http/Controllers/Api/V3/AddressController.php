<?php

namespace App\Http\Controllers\Api\V3;

use Illuminate\Http\Request;
use App\Models\IndonesiaProvince;
use App\Models\IndonesiaCity;
use App\Models\IndonesiaDistrict;
use App\Models\IndonesiaVillage;

class AddressController extends Controller
{
    public function getProvinces(Request $request) {
        $items = IndonesiaProvince::get();
        return response()->json($items);
    }
    
    public function getCities(Request $request, $province_code = null) {
        $items = IndonesiaCity::where('province_code', $province_code)->get();
        return response()->json($items);
    }

    public function getDistricts(Request $request, $city_code = null) {
        $items = IndonesiaDistrict::where('city_code', $city_code)->get();
        return response()->json($items);
    }

    public function getSubdistricts(Request $request, $district_code = null) {
        $items = IndonesiaVillage::where('district_code', $district_code)->get();
        return response()->json($items);
    }
}
