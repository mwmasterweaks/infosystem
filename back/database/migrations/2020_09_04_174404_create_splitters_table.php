<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSplittersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('splitters', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('attach_to');
            $table->integer('attach_id');
            $table->string('splitter_type_id');
            $table->string('port_type');
            $table->string('parent');
            $table->integer('parent_id');
            $table->integer('attached_port');
            $table->string('status')->default('Not Active');
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
        Schema::dropIfExists('splitters');
    }
}
