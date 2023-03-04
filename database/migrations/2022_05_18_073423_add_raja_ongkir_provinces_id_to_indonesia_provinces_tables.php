<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRajaOngkirProvincesIdToIndonesiaProvincesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('indonesia_provinces', function (Blueprint $table) {
            $table->integer('rajaongkir_provinces_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('indonesia_provinces', function (Blueprint $table) {
            $table->dropColumn(['rajaongkir_provinces_id']);
        });
    }
}
