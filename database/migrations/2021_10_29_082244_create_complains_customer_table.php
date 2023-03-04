<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComplainsCustomerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('complains', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('order_id');
            $table->integer('order_detail_id');
            $table->integer('product_id');
            $table->integer('user_id');
            $table->integer('seller_id');
            $table->text('reason');
            $table->enum('status', ['completed', 'requested', 'accepted', 'procceeded']);
            $table->timestamps();
        });
        
        Schema::create('complain_images', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('complain_id');
            $table->integer('thumbnail_img');
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
        Schema::dropIfExists('complains');
        Schema::dropIfExists('complain_images');
    }
}
