<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMovementBankColumnToSalesAndPaymentCreditTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->unsignedBigInteger('bank_movement_id')->nullable();
            $table->foreign('bank_movement_id')->references('id')->on('bank_movements');
        });

        Schema::table('payment_credits', function (Blueprint $table) {
            $table->unsignedBigInteger('bank_movement_id')->nullable();
            $table->foreign('bank_movement_id')->references('id')->on('bank_movements');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales', function (Blueprint $table) {
            //
        });
    }
}
