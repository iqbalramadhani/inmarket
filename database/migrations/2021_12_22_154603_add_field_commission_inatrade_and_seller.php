<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldCommissionInatradeAndSeller extends Migration
{
    public function up()
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->double('total_commission_seller', 20, 2)->default(0)->nullable();
            $table->double('total_commission_inatrade', 20, 2)->default(0)->nullable();
        });
    }

    public function down()
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->dropColumn([
               'total_commission_seller',
               'total_commission_inatrade'
            ]);
        });
    }
}