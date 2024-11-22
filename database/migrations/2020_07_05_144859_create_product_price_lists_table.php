<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductPriceListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_price_lists', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('description', 50);
            $table->string('detail', 100)->nullable();
            $table->decimal('price', 10,2);
            $table->timestamps();
            $table->unsignedBigInteger('product_id');

            $table->foreign('product_id')->references('id')->on('products');
            /*$table->unsignedBigInteger('operation_type_id');
            $table->unsignedBigInteger('operation_type_id');
            $table->foreign('operation_type_id')->references('id')->on('operations_type');
            $table->foreign('');*/
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_price_lists');
    }
}
