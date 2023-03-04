<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldAgreeTostoUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_agree_tos_customer')->nullable()->default(false);
            $table->boolean('is_agree_tos_seller')->nullable()->default(false);
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_agree_tos_customer', 'is_agree_tos_seller']);
        });
    }
}