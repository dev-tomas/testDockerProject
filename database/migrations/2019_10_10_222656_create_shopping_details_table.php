<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShoppingDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shopping_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->decimal('quantity', 10,2);
            $table->decimal('unit_value',10,2);
            $table->decimal('unit_price',10,2);
            $table->decimal('subtotal',10,2);
            $table->decimal('total',10,2);
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('shopping_id');
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('shopping_id')->references('id')->on('shoppings');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shopping_details');
    }
}
