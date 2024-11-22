<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEntriesColumnToLiquidationCashesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('liquidation_cashes', function (Blueprint $table) {
            $table->decimal('entries', 10,2)->nullable()->after('output');
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
            $table->decimal('entries');
        });
    }
}
