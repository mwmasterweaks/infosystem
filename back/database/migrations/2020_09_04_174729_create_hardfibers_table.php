<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHardfibersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hardfibers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string("type");
            $table->string("end1")->nullable();
            $table->integer("end1_id")->nullable();
            $table->string("end2")->nullable();
            $table->integer("end2_id")->nullable();
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
        Schema::dropIfExists('hardfibers');
    }
}
