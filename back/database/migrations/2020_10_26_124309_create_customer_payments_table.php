<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('client_id');
            $table->integer('user_id');
            $table->integer('payment_method_id')->nullable();
            $table->integer('banking_payment_code_id')->nullable();
            $table->double('amount')->nullable();
            $table->date('date')->nullable();
            $table->string('or_number')->nullable();
            $table->string('remarks');
            $table->string('tbl_name')->default("customer_payment");
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
        Schema::dropIfExists('customer_payments');
    }
}
