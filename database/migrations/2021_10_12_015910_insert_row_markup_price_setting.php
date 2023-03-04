<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InsertRowMarkupPriceSetting extends Migration
{
    public function up()
    {
        Schema::table('business_settings', function (Blueprint $table) {
           DB::select("INSERT INTO `business_settings` (`type`, `value`, `lang`, `created_at`, `updated_at`) VALUES ('markup_price', '{\"enable\":false,\"type\":null,\"value\":null}', 'id', '2021-09-05 15:42:20', '2021-09-05 15:42:55');");
        });
    }

    public function down()
    {
        Schema::table('business_settings', function (Blueprint $table)
        {
            //
        });
    }
}