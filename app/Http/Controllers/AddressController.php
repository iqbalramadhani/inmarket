<?php

namespace App\Http\Controllers;

use App\Address;
use App\Models\City;
use App\Models\District;
use App\Models\PostalCode;
use App\Models\Province;
use App\Models\SubDistrict;
use Auth;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $address = new Address;
        if ($request->has('customer_id')) {
            $address->user_id = $request->customer_id;
        } else {
            $address->user_id = Auth::user()->id;
        }
        $address->address = $request->address;
        $address->country = $request->country;
        if ($request->country == 'Indonesia') {
            $address->province_id = $request->province;
            $address->city_id = $request->city;
            $address->subdistrict_id = $request->sub_district;
            $address->postal_code = $request->postal_code;
        }

        $address->detail = $request->detail;
        $address->longitude = $request->longitude;
        $address->latitude = $request->latitude;
        $address->phone = $request->phone;
        $address->save();

        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['address_data'] = $address = Address::findOrFail($id);
        $data['provinces'] = Province::get();

        if($address->country == 'Indonesia') {

            $city = City::where('name', $address->city)->first();
            $district = District::where('name', $address->district)->first();
            $sub_district = SubDistrict::where('name', $address->sub_district)->first();
            $province = Province::where('name', $address->province)->first();
            $postal_code = PostalCode::where('kodepos', $address->postal_code)->first();

            $data['locations'] = [
                'cities' => City::where('province_id', $city->province_id)->get(),
                'districts' => District::where('kota_id', $district->kota_id)->get(),
                'provinces' => Province::get(),
                'sub_districts' => SubDistrict::where('kecamatan_id', $sub_district->kecamatan_id)->get(),
                'postal_codes' => PostalCode::where('kelurahan_id', $postal_code->kelurahan_id)->get(),
            ];

        } else {
            $data['locations'] = [
                'cities' => [],
                'districts' => [],
                'provinces' => [],
                'sub_districts' => [],
                'postal_codes' => [],
            ];
        }

        // $province = Province::where('')
        // $data['province'] = Province::get();

        // dd($data);

        $returnHTML = view('frontend.user.address.edit_address_modal', $data)->render();
        return response()->json(array('data' => $data, 'html' => $returnHTML));
//        return ;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $address = Address::findOrFail($id);

        $address->address = $request->address;
        $address->country = $request->country;

        $address->address = $request->address;
        $address->country = $request->country;
        if ($request->country == 'Indonesia') {
            $address->city = City::where('id', $request->city)->first()->name;
            $address->province = Province::where('id', $request->province)->first()->name;
            $address->district = District::where('id', $request->district)->first()->name;
            $address->sub_district = SubDistrict::where('id', $request->sub_district)->first()->name;
            $address->postal_code = PostalCode::where('id', $request->postal_code)->first()->kodepos;
        } else {
            $address->city = $request->city;
            $address->province = $request->province;
            $address->sub_district = $request->sub_district;
            $address->district = $request->district;
            $address->postal_code = $request->postal_code;
            $address->postal_code = $request->postal_code;
        }

        $address->detail = $request->detail;
        $address->longitude = $request->longitude;
        $address->latitude = $request->latitude;
        $address->phone = $request->phone;
        $address->save();

        flash(translate('Address info updated successfully'))->warning();
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $address = Address::findOrFail($id);
        if (!$address->set_default) {
            $address->delete();
            return back();
        }
        flash(translate('Default address can not be deleted'))->warning();
        return back();
    }

    public function set_default($id)
    {
        foreach (Auth::user()->addresses as $key => $address) {
            $address->set_default = 0;
            $address->save();
        }
        $address = Address::findOrFail($id);
        $address->set_default = 1;
        $address->save();

        return back();
    }
}
