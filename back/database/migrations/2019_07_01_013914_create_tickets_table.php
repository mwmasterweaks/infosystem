<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('ticket_id');
            $table->bigInteger('client_id');
            $table->bigInteger('user_id');
            $table->bigInteger('area_id');
            $table->string('updated_by')->nullable();
            $table->string('complain', 2000);
            $table->string('findings', 2000);
            $table->string('action', 2000);
            $table->string('remarks', 2000)->nullable();
            $table->string('report', 2000)->nullable();
            $table->integer('prev_status')->nullable();
            $table->bigInteger('Status_Ticket_id');
            $table->string('connection_status')->default('down');
            $table->string('technical_assigned')->nullable();
            $table->date('target_date')->nullable();
            $table->integer('team_assigned')->nullable();
            $table->integer('downtime_hours')->nullable();
            $table->dateTime('date_time_fixed')->nullable();
            $table->boolean('to_soa')->nullable();
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
        Schema::dropIfExists('tickets');
    }
}
