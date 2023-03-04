<?php

use Illuminate\Database\Seeder;
use App\BusinessSetting;

class UpdateAdminConfiguration extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $links = BusinessSetting::where('type', 'header_menu_links')->first();

        
        if($links) {
            BusinessSetting::find($links->id)->update([
                'value' => '[null,"flash-deals","blog","brands","categories"]',
            ]);
        }
    }
}
