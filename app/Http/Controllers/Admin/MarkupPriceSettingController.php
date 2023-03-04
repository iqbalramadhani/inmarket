<?php

namespace App\Http\Controllers\Admin;

use App\Models\BusinessSetting;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Artisan;

class MarkupPriceSettingController extends Controller
{
    public function index()
    {
        $setting = BusinessSetting::where('type', 'markup_price')->first();
        return view('backend.markup_price.index', ['setting' => $setting]);
    }

    public function update(Request $request)
    {
        $enable = false;
        if($request->enable === 'on'){
            $enable = true;
        }

        $markup = json_encode([
            'enable' => $enable,
            'type' => $request->type,
            'value' => $request->markup_price
        ]);

        BusinessSetting::where('type', 'markup_price')->update([
            'value' => $markup
        ]);

        BusinessSetting::where('type', 'vendor_commission')->update([
            'value' => $request->markup_price
        ]);

        Artisan::call('cache:clear');

        flash(translate("Settings updated successfully"))->success();

        return back();
    }
}