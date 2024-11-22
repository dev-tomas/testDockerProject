<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToPurchaseCreditPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_credit_payments', function (Blueprint $table) {
            $table->dropColumn('expiration');
            $table->dropColumn('quote');
            $table->dropColumn('paid');
            
            $table->unsignedBigInteger('cash_id')->nullable();
            $table->unsignedBigInteger('payment_method_id')->nullable();
            $table->unsignedBigInteger('bank_account_id')->nullable();

            $table->foreign('cash_id')->references('id')->on('cashes');
            $table->foreign('payment_method_id')->references('id')->on('payment_methods');
            $table->foreign('bank_account_id')->references('id')->on('bank_accounts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_credit_payments', function (Blueprint $table) {
            //
        });
    }
}
