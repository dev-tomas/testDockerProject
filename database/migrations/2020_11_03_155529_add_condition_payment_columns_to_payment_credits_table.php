<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddConditionPaymentColumnsToPaymentCreditsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payment_credits', function (Blueprint $table) {
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
        Schema::table('payment_credits', function (Blueprint $table) {
            $table->dropForeign('cash_id');
            $table->dropForeign('payment_method_id');
            $table->dropForeign('bank_account_id');
            $table->dropColumn('cash_id');
            $table->dropColumn('payment_method_id');
            $table->dropColumn('bank_account_id');
        });
    }
}
