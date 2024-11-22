<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTotalCreditColumnLiquidationCashesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('liquidation_cashes', function (Blueprint $table) {
            $table->float('total_credits')->default(0);
            $table->float('payment_credits')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('liquidation_cashes', function (Blueprint $table) {
            //
        });
    }
}
