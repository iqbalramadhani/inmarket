<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAdressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('Addresses', function (Blueprint $table) {
            //
            $table->string('province', 255)->nullable();
            $table->string('district', 255)->nullable();
            $table->string('sub_district', 255)->nullable();
            $table->string('detail', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('Addresses', function (Blueprint $table) {
            //
            $table->dropColumn(['province', 'district', 'sub_district', 'detail']);
        });
    }
}
