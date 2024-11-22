<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPosColumnsToSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->decimal('total_paying', 10,2)->nullable()->after('total');
            $table->decimal('balance', 10,2)->nullable()->after('total_paying');
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
            $table->decimal('total_paying', 10,2)->nullable()->after('total');
            $table->decimal('balance', 10,2)->nullable()->after('total_paying');
        });
    }
}
