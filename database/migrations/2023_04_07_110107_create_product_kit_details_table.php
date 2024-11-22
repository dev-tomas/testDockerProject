<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductKitDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_kit_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->decimal('quantity', 10, 2);
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('product_kit_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_kit_details');
    }
}
