<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateShopTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->string('province', 50);
            $table->string('city', 50);
            $table->string('district', 50);
            $table->string('sub_district', 50);
            $table->string('postal_code', 10);
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
            //
            $table->dropColumns(['province', 'city', 'district', 'sub_district', 'postal_code']);
        });
    }
}
