<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\AddressCollection;
use App\Address;
use Illuminate\Http\Request;
use App\Models\Province;
use App\Models\City;
use App\Models\District;
use App\Models\SubDistrict;
use App\Models\PostalCode;

class AddressController extends Controller
{
    public function addresses($id)
    {
        return new AddressCollection(Address::where('user_id', $id)->get());
    }

    public function createShippingAddress(Request $request)
    {
        $address = new Address;
        $address->user_id = $request->user_id;
        $address->address = $request->address;
        $address->country = $request->country;
        $address->city = $request->city;
        $address->postal_code = $request->postal_code;
        $address->phone = $request->phone;
        $address->save();

        return response()->json([
            'message' => 'Shipping information has been added successfully'
        ]);
    }

    public function deleteShippingAddress($id)
    {
        $address = Address::findOrFail($id);
        $address->delete();
        return response()->json([
            'message' => 'Shipping information has been added deleted'
        ]);
    }

    public function getProvinces(Request $request) {
        $items = Province::get();
        return response()->json($items);
    }
    
    public function getCities(Request $request, $id_provinsi=null) {
        $items = City::where('province_id', $id_provinsi)->get();
        return response()->json($items);
    }
    public function getDistricts(Request $request, $id_kota=null) {
        $items = District::where('kota_id', $id_kota)->get();
        return response()->json($items);
    }
    public function getSubDistricts(Request $request, $id_kecamatan=null) {
        $items = SubDistrict::where('kecamatan_id', $id_kecamatan)->get();
        return response()->json($items);
    }
    public function getPostalCodes(Request $request, $id_kelurahan=null) {
        $items = PostalCode::where('kelurahan_id', $id_kelurahan)->get();
        return response()->json($items);
    }
}
