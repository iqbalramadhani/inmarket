<?php

use Illuminate\Database\Seeder;
use App\BusinessSetting;

class PaymentGatewaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {   
        $oy = BusinessSetting::where('type', 'oyid_payment')->first();
        $wallet = BusinessSetting::where('type', 'wallet_system')->first();
        if($oy) {
            BusinessSetting::find($oy->id)->update([
                'value' => '1',
            ]);
        } else {
            BusinessSetting::create([
                'type' => 'oyid_payment',
                'value' => '1',
                'lang'  => null
            ]);
        }
        
        if($wallet) {
            BusinessSetting::find($oy->id)->update([
                'value' => '1',
            ]);
        }
    }
}
