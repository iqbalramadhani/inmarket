<?php

namespace App\Http\Controllers\Sellers;

use App\Http\Controllers\Controller;
use App\Shop;
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
        $shop->save();

        flash(translate('Your Shop has been updated successfully!'))->success();
        return back();

    }
}