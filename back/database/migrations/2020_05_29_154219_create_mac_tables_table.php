<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMacTablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mac_tables', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('pmx_ip')->nullable();
            $table->string('ip');
            $table->string('mac_desc');
            $table->string('port_desc');
            $table->string('mac_address_main')->unique();
            $table->string('mac_address_first');
            $table->integer('mac_address_second');
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
        Schema::dropIfExists('mac_tables');
    }
}
