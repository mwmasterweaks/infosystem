<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWhtsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('whts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('client_id');
            $table->date('date');
            $table->string('description')->nullable();
            $table->double('amount');
            $table->string('tbl_name')->default("wht");
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
        Schema::dropIfExists('whts');
    }
}
