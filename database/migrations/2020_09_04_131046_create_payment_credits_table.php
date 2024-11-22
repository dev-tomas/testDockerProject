<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentCreditsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_credits', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('date');
            $table->string('bank')->nullable();
            $table->string('operation_bank')->nullable();
            $table->string('payment_type');
            $table->decimal('payment', 10,2);
            $table->unsignedBigInteger('credit_client_id')->nullable();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->timestamps();

            $table->foreign('credit_client_id')->references('id')->on('credit_clients');
            $table->foreign('client_id')->references('id')->on('clients');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_credits');
    }
}
