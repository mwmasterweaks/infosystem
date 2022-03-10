<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('account_no')->unique();
            $table->integer('region_id');
            $table->integer('area_id');
            $table->string('name');
            $table->string('owner_name');
            $table->string('location');
            $table->string('contact_person')->nullable();
            $table->string('contact')->nullable();
            $table->string('email_add')->nullable();
            $table->date('contract')->nullable();
            $table->string('remarks')->nullable();
            $table->string('term')->nullable();
            $table->double('OTC')->nullable();
            $table->integer('sales_id')->nullable();
            $table->integer('sales_agent_id')->nullable();
            $table->integer('engineers_id')->nullable();
            $table->string('package_id')->nullable();
            $table->string('modem_id')->nullable();
            $table->string('communication_protocol')->nullable();
            $table->string('package_type_id')->nullable();
            $table->string('pon_id')->nullable();
            $table->string('onu')->nullable();
            $table->string('modem_mac_address')->nullable();
            $table->string('vlan')->nullable();
            $table->string('ip_assigned')->nullable();
            $table->date('date_assigned')->nullable();
            $table->string('status')->nullable();
            $table->date('date_activated')->nullable();
            $table->boolean('wfs')->default(false);
            $table->decimal('lat', 10, 8)->nullable();
            $table->decimal('lng', 11, 8)->nullable();
            $table->integer('bucket_id')->nullable();
            $table->string('subscription_name')->nullable();
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
        Schema::dropIfExists('clients');
    }
}
