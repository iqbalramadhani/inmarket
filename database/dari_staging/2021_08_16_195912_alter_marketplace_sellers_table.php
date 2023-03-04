<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterMarketplaceSellersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('marketplace_sellers', function (Blueprint $table) {
            $table->string('sub_district')->after('city')->nullable();
            $table->string('village')->after('sub_district')->nullable();
            $table->string('shop_type')->after('shop_title')->nullable();
            $table->string('turnover')->after('shop_type')->nullable();
            $table->string('capital')->after('turnover')->nullable();
            $table->string('human_resources')->after('capital')->nullable();
            $table->string('category')->after('human_resources')->nullable();
            $table->tinyInteger('has_online_shop')->after('category')->default(1);
            $table->string('online_shop_platforms')->after('has_online_shop')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('marketplace_sellers', function (Blueprint $table) {
            $table->dropColumn('sub_district');
            $table->dropColumn('village');
            $table->dropColumn('shop_type');
            $table->dropColumn('turnover');
            $table->dropColumn('capital');
            $table->dropColumn('human_resources');
            $table->dropColumn('category');
            $table->dropColumn('has_online_shop');
            $table->dropColumn('online_shop_platforms');
        });
    }
}
