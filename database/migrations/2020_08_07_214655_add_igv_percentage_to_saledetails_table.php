<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIgvPercentageToSaledetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('saledetails', function (Blueprint $table) {
            $table->decimal('igv_percentage', 10,2)->nullable();
            $table->decimal('price_unit',10,2)->nullable();
            $table->unsignedBigInteger('typeaffectations_id')->nullable();

            $table->foreign('typeaffectations_id')->references('id')->on('typeaffectations');
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
