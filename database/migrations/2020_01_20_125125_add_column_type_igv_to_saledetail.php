<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnTypeIgvToSaledetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('saledetails', function (Blueprint $table) {
            $table->unsignedBigInteger('type_igv_id')->after('product_id')->nullable();
            $table->foreign('type_igv_id')->references('id')->on('igv_type');
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
            $table->unsignedBigInteger('type_igv_id')->after('product_id');
        });
    }
}
