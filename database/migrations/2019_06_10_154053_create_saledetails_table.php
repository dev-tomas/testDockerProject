<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSaledetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('saledetails', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->decimal('price');
            $table->decimal('quantity');
            $table->decimal('igv');
            $table->decimal('total');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('sale_id');

            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('sale_id')->references('id')->on('sales');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('salesdetails');
    }
}
