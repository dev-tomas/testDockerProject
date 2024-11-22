<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsExchangeRateToClients extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->decimal('exchange_rate_purchase', 10,3)->nullable();
            $table->decimal('exchange_rate_sale', 10,3)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->decimal('exchange_rate_purchase', 10,3)->nullable();
            $table->decimal('exchange_rate_sale', 10,3)->nullable();
        });
    }
}
