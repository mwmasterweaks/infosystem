<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHardfiberCoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hardfiber_cores', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer("hardfiber_id");
            $table->string("buffer_color");
            $table->string("core_color");
            $table->string("going")->nullable();
            $table->integer("going_id")->nullable();
            $table->string("end_type");
            $table->float("los")->nullable();
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
        Schema::dropIfExists('hardfiber_cores');
    }
}
