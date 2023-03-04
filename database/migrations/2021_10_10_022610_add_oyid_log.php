<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOyidLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pg_oy_log', function (Blueprint $table) {
            //
            $table->increments('id');
            $table->string('partnerTxId', 100);
            $table->string('trx_id', 100);
            $table->string('status', 30);
            $table->string('url', 250)->nullable();
            $table->text('request')->nullable();
            $table->text('response')->nullable();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::drop('pg_oy_log');
    }
}
