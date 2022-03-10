<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSplitterPortsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('splitter_ports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('splitter_id');
            $table->integer('port_number');
            $table->string('going')->nullable();
            $table->integer('going_id')->nullable();
            $table->float('los')->nullable();
            $table->string('port_status')->default('PU');
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
        Schema::dropIfExists('splitter_ports');
    }
}
