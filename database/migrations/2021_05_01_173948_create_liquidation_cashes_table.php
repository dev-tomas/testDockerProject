<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLiquidationCashesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('liquidation_cashes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->decimal('exchange_rate', 10,2)->nullable();
            $table->string('transaction');
            $table->string('factura_start');
            $table->string('factura_end');
            $table->string('boleta_start');
            $table->string('boleta_end');
            $table->decimal('total_factura', 10,2);
            $table->decimal('total_boleta', 10,2);
            $table->decimal('efectivo', 10,2);
            $table->decimal('tarjeta_credito', 10,2);
            $table->decimal('tarjeta_debito', 10,2);
            $table->decimal('deposito_cuenta', 10,2);
            $table->decimal('opening_amount', 10,2);
            $table->decimal('paid_cash', 10,2);
            $table->decimal('output', 10,2);
            $table->decimal('total', 10,2);
            $table->unsignedBigInteger('client_id')->nullable();
            $table->unsignedBigInteger('cash_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();

            $table->foreign('cash_id')->references('id')->on('cashes');
            $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('liquidation_cashes');
    }
}
