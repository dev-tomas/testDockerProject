<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDecimalsToSaledetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('saledetails', function (Blueprint $table) {
            $table->decimal('price', 10,5)->change();
            $table->decimal('igv', 10,5)->change();
            $table->decimal('total', 10,5)->change();
            $table->decimal('subtotal', 10,5)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('saledetails', function (Blueprint $table) {
            //
        });
    }
}
