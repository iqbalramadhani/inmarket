<?php

namespace App\Http\Controllers\Admin;

use App\BusinessSetting;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AutoCheckOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $settings = BusinessSetting::whereIn('type', ['autoconfirm_order', 'autocancel_order'])->get();
        // dd($settings->isEmpty());
        if($settings->isEmpty()) {
            BusinessSetting::insert([
                // 'enable' => true,
                'type' => 'autoconfirm_order',
                'value' => 2,
            ]);
            BusinessSetting::insert([
                // 'enable' => true,
                'type' => 'autocancel_order',
                'value' => 2,
            ]);

            $settings = BusinessSetting::whereIn('type', ['autoconfirm_order', 'autocancel_order'])->get();
        }

        // dd($settings);
        foreach ($settings as $setting) {
            if (!$setting->value) {
                $setting->value = json_encode([
                    // 'enable' => false,
                    'type' => null,
                    'value' => null,
                ]);
                $setting->save();
            } else {
                BusinessSetting::create([
                    // 'enable' => true,
                    'type' => 'autoconfirm_order',
                    'value' => 2,
                ]);
                BusinessSetting::create([
                    // 'enable' => true,
                    'type' => 'autocancel_order',
                    'value' => 2,
                ]);
            }
        }
        // dd($settings);
        return view('backend.autocheck_order.index', ['settings' => $settings]);
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
        //
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
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        BusinessSetting::where('type', 'autoconfirm_order')->first()->update([
            'value' => $request->autoconfirm_order,
        ]);
        
        BusinessSetting::where('type', 'autocancel_order')->first()->update([
            'value' => $request->autocancel_order,
        ]);

        return redirect()->route('autocheck-setting.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
