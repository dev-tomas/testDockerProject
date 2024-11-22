<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('description', 255);
            $table->string('code', 100);
            $table->string('internalcode', 200);
            $table->decimal('price', 10,2);
            $table->boolean('status');
            $table->timestamps();
            $table->unsignedBigInteger('measure_id')->nullable();
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('brand_id');


            $table->foreign('measure_id')->references('id')->on('measures');
            $table->foreign('category_id')->references('id')->on('categories');
            $table->foreign('client_id')->references('id')->on('clients');
            $table->foreign('brand_id')->references('id')->on('brands');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
