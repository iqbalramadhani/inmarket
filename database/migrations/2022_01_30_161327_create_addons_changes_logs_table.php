<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddonsChangesLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addons_changes_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('addon_id');
            $table->text('file_changes_directory');
            $table->text('file_before_changes')->nullable();
            $table->enum('status', ['new', 'modified'])->nullable();
            $table->timestamps();
            $table->softDeletes(); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('addons_changes_logs');
    }
}
