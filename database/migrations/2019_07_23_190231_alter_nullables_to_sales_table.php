<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterNullablesToSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->decimal('exonerated')->nullable()->change();
            $table->boolean('paidout')->nullable()->change();
            $table->boolean('productregion')->nullable()->change();
            $table->boolean('serviceregion')->nullable()->change();
            $table->boolean('detraction')->nullable()->change();
            $table->boolean('detraction')->nullable()->change();
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
