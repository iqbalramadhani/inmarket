<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Address;
use App\Models\City;
use App\Models\State;
use Auth;
use App\Models\RajaOngkirCity;
use App\Models\RajaOngkirSubdistrict;
use App\Models\RajaOngkirProvince;
use App\Models\IndonesiaProvince;
use App\Models\IndonesiaCity;
use App\Models\IndonesiaDistrict;
use App\Models\IndonesiaVillage;

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
        if($request->has('customer_id')){
            $address->user_id   = $request->customer_id;
        }
        else{
            $address->user_id   = Auth::user()->id;
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
        $data['provinces'] = RajaOngkirProvince::get();

        if($address->country == 'Indonesia') {

            $city = RajaOngkirCity::where('city_id', $address->city_id)->first();
            $sub_district = RajaOngkirSubdistrict::where('subdistrict_id', $address->subdistrict_id)->first();

            if(!$city){
                $province_id = NULL;
            }else{
                $province_id = $address->province_id;
            }
            if(!$sub_district){
                $sub_district_id = NULL;
            }else{
                $sub_district_id = $address->city_id;
            }

            $data['locations'] = [
                'provinces' => RajaOngkirProvince::orderBy('province_name', 'ASC')->get(),
                'cities' => RajaOngkirCity::where('province_id', $province_id)->orderBy('city_name', 'ASC')->get(),
                'sub_districts' => RajaOngkirSubdistrict::where('city_id', $sub_district_id)->orderBy('subdistrict_name', 'ASC')->get(),
                'postal_codes' => $address->postal_code
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
        
        $returnHTML = view('frontend.partials.address_edit_modal', $data)->render();
        return response()->json(array('data' => $data, 'html'=>$returnHTML));
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
        $address->city_id = $request->city;
        $address->province_id = $request->province;
        $address->subdistrict_id = $request->sub_district;
        $address->postal_code = $request->postal_code;

        $address->detail = $request->detail;
        $address->phone = $request->phone;
        $address->save();

        flash(translate('Address info updated successfully'))->success();
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
        if(!$address->set_default){
            $address->delete();
            return back();
        }
        flash(translate('Default address can not be deleted'))->warning();
        return back();
    }

    public function getStates(Request $request) {
        $states = State::where('status', 1)->where('country_id', $request->country_id)->get();
        $html = '<option value="">'.translate("Select State").'</option>';
        
        foreach ($states as $state) {
            $html .= '<option value="' . $state->id . '">' . $state->name . '</option>';
        }
        
        echo json_encode($html);
    }
    
    public function getCities(Request $request) {
        $cities = City::where('status', 1)->where('state_id', $request->state_id)->get();
        $html = '<option value="">'.translate("Select City").'</option>';
        
        foreach ($cities as $row) {
            $html .= '<option value="' . $row->id . '">' . $row->getTranslation('name') . '</option>';
        }
        
        echo json_encode($html);
    }

    public function set_default($id){
        foreach (Auth::user()->addresses as $key => $address) {
            $address->set_default = 0;
            $address->save();
        }
        $address = Address::findOrFail($id);
        $address->set_default = 1;
        $address->save();

        return back();
    }

    public function getProvinces(Request $request) {
        $items = IndonesiaProvince::get();
        return response()->json($items);
    }
    
    public function getCitiesNew(Request $request, $province_code = null) {
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
