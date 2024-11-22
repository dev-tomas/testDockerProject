<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransferDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfer_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->decimal('quantity', 10,2);
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('transfer_id');
            $table->timestamps();

            $table->foreign('transfer_id')->references('id')->on('transfers');
            $table->foreign('product_id')->references('id')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transfer_details');
    }
}
