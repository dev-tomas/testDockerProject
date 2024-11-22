<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseAccountTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_account', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('account');
            $table->unsignedBigInteger('shopping_id')->nullable();
            $table->timestamps();

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
        Schema::dropIfExists('purchase_account');
    }
}
