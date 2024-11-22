<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesPaymentMethodTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_payment_method', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->decimal('amount', 10,2)->nullable();
            $table->longText('observation')->nullable();
            $table->longText('payment_type')->nullable();
            $table->unsignedBigInteger('sale_id');
            $table->timestamps();

            $table->foreign('sale_id')->references('id')->on('sales')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales_payment_method');
    }
}
