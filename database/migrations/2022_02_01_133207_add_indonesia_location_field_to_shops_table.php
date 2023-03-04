<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndonesiaLocationFieldToShopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->unsignedBigInteger('indonesia_province_id')->nullable()->after('couriers');
            $table->unsignedBigInteger('indonesia_city_id')->nullable()->after('indonesia_province_id');
            $table->unsignedBigInteger('indonesia_district_id')->nullable()->after('indonesia_city_id');
            $table->unsignedBigInteger('indonesia_subdistrict_id')->nullable()->after('indonesia_district_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->dropColumn(['indonesia_province_id']);
            $table->dropColumn(['indonesia_city_id']);
            $table->dropColumn(['indonesia_district_id']);
            $table->dropColumn(['indonesia_subdistrict_id']);
        });
    }
}
