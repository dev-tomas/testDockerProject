<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCreditClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('credit_clients', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('date');
            $table->decimal('total', 10,2);
            $table->char('status', 1);
            $table->date('expiration');
            $table->decimal('debt', 10,2);
            $table->char('send_email', 1)->default(0);
            $table->unsignedBigInteger('sale_id')->nullable();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->timestamps();
            $table->foreign('sale_id')->references('id')->on('sales');
            $table->foreign('client_id')->references('id')->on('clients');
            $table->foreign('customer_id')->references('id')->on('customers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('credit_clients');
    }
}
