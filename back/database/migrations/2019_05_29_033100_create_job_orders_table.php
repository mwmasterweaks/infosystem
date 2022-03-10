<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateJobOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('jo_num')->nullable();;
            $table->integer('client_id');
            $table->integer('client_detail_id')->nullable();
            $table->integer('region_id');
            $table->string('details')->nullable();
            $table->string('location')->nullable();
            $table->string('project_description')->nullable();
            $table->string('cable_category')->nullable();
            $table->string('foc_length')->nullable();
            $table->date('started')->nullable();
            $table->date('finished')->nullable();
            $table->integer('engineer_in_charge');
            $table->integer('prepare');
            $table->integer('approve');
            $table->integer('note');
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
        Schema::dropIfExists('job_orders');
    }
}
