<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldPartnerOyToOy extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pg_oy_log', function (Blueprint $table) {
            $table->renameColumn('partnerTxId', 'partner_trx_id');
            $table->string('trx_id', 100)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pg_oy_log', function (Blueprint $table) {
            $table->renameColumn('partner_trx_id', 'partnerTxId');
            $table->string('trx_id', 100)->change();
        });
    }
}
