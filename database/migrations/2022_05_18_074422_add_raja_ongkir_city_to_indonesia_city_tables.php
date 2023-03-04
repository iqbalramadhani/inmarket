<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRajaOngkirCityToIndonesiaCityTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('indonesia_cities', function (Blueprint $table) {
            $table->integer('rajaongkir_cities_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('indonesia_cities', function (Blueprint $table) {
            $table->dropColumn(['rajaongkir_cities_id']);
        });
    }
}
