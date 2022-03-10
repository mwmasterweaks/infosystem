<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePonsTable extends Migration
{
    public function up()
    {
        Schema::create('pons', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('olt_id');
            $table->string("pon");
            $table->string("area")->nullable();
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
        Schema::dropIfExists('pons');
    }
}
