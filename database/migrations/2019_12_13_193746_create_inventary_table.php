<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInventaryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->date('admission');
            $table->string('serie');
            $table->string('correlative');
            $table->string('place')->nullable();
            $table->string('serial')->nullable();
            $table->string('lot')->nullable();
            $table->string('expiration')->nullable();
            $table->string('warranty')->nullable();
            $table->integer('amount_entered')->nullable();
            $table->string('responsable', 255)->nullable();
            $table->string('observation', 255)->nullable();

            $table->unsignedBigInteger('shopping_id');
            $table->unsignedBigInteger('provider_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('warehouse_id');
            $table->unsignedBigInteger('client_id');
            $table->timestamps();
            
            $table->foreign('warehouse_id')->references('id')->on('warehouses');
            $table->foreign('client_id')->references('id')->on('clients');
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('provider_id')->references('id')->on('providers');
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
        Schema::dropIfExists('inventory');
    }
}
