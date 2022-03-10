<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket_groups', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('ticket_id');
            $table->bigInteger('user_id');
            $table->string('updated_by')->nullable();
            $table->string('complain')->nullable();
            $table->string('findings')->nullable();
            $table->string('action')->nullable();
            $table->integer('complaint_new')->nullable();
            $table->longText('bwmon')->nullable();
            $table->longText('device')->nullable();
            $table->longText('loss')->nullable();
            $table->longText('downtime')->nullable();
            $table->longText('cacti')->nullable();
            $table->longText('rep_findings')->nullable();
            $table->longText('rep_action')->nullable();
            $table->integer('rebatable')->nullable();
            $table->longText('remarks');
            $table->longText('report');
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
        Schema::dropIfExists('ticket_groups');
    }
}
