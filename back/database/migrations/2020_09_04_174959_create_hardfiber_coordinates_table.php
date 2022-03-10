<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHardfiberCoordinatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hardfiber_coordinates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('hardfiber_id');
            $table->decimal('lat', 10, 8);
            $table->decimal('lng', 11, 8);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hardfiber_coordinates');
    }
}
