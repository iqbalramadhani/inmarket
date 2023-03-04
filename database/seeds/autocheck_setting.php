<?php

use Illuminate\Database\Seeder;
use App\BusinessSetting;

class autocheck_setting extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
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
    }
}
