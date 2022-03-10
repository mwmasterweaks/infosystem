<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateClientDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('client_id');
            $table->string('otc')->nullable();
            $table->boolean('contract_status')->nullable();
            $table->boolean('mapping_status')->nullable();
            $table->string('cable_category', 20)->nullable();
            $table->string('foc_length', 20)->nullable();
            $table->string('foc_layout', 20)->nullable();
            $table->date('foc_schedule')->nullable();
            $table->dateTime('foc_plan_duration')->nullable();
            $table->string('layout_remarks')->nullable();
            $table->boolean('modem_status')->nullable();
            $table->date('applied_date')->nullable();
            $table->string('inst_remarks')->nullable();
            $table->date('target_date')->nullable();
            $table->date('aging')->nullable();
            $table->date('date_activated')->nullable();
            $table->string('status', 20)->nullable();
            $table->boolean('line_transfer')->nullable();
            $table->integer('team_assigned')->nullable();
            $table->string('layout_status')->nullable();
            $table->timestamps();
        });

        DB::statement("ALTER TABLE `client_details` ADD UNIQUE(`client_id`);");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('client_details');
    }
}
