<?php

namespace App\Http\Controllers\Sellers;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use Illuminate\Http\Request;

class SettingCourierController extends Controller
{
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'couriers' => 'required'
        ]);

        $shop = Shop::find($id);
        $shop->couriers = json_encode($request->couriers);
        $shop->city_id = $request->city;
        $shop->province_id = $request->province;
        $shop->subdistrict_id = $request->sub_district;
        $shop->postal_code_pickup = $request->postal_code;
        $shop->address_pickup = $request->address;

        $shop->save();

        flash(translate('Your Shop has been updated successfully!'))->success();
        return back();

    }
}