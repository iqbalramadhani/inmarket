<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InsertRowInamarketVersionSetting extends Migration
{
    public function up()
    {
        Schema::table('business_settings', function (Blueprint $table) {
           DB::select("INSERT INTO `business_settings` (`type`, `value`, `lang`, `created_at`, `updated_at`) VALUES ('inamarket_version', '1.0.0', 'id', '2022-03-18 15:42:20', '2021-03-18 15:42:55');");
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
