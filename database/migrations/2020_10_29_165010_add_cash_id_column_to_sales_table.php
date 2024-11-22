<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCashIdColumnToSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->unsignedBigInteger('cash_id')->nullable();
            $table->unsignedBigInteger('payment_method_id')->nullable();
            $table->unsignedBigInteger('bank_account_id')->nullable();

            $table->unsignedBigInteger('other_cash_id')->nullable();
            $table->unsignedBigInteger('other_payment_method_id')->nullable();
            $table->unsignedBigInteger('other_bank_account_id')->nullable();
            
            $table->foreign('cash_id')->references('id')->on('cashes');
            $table->foreign('payment_method_id')->references('id')->on('payment_methods');
            $table->foreign('bank_account_id')->references('id')->on('bank_accounts');

            $table->foreign('other_cash_id')->references('id')->on('cashes');
            $table->foreign('other_payment_method_id')->references('id')->on('payment_methods');
            $table->foreign('other_bank_account_id')->references('id')->on('bank_accounts');
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
            $table->dropForeign('cash_id');
            $table->dropForeign('payment_method_id');
            $table->dropForeign('bank_account_id');
            $table->dropColumn('cash_id');
            $table->dropColumn('payment_method_id');
            $table->dropColumn('bank_account_id');

            $table->dropForeign('other_cash_id');
            $table->dropForeign('other_payment_method_id');
            $table->dropForeign('other_bank_account_id');
            $table->dropColumn('other_cash_id');
            $table->dropColumn('other_payment_method_id');
            $table->dropColumn('other_bank_account_id');
        });
    }
}
