<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRequirementDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requirement_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('requirement_id');
            $table->unsignedBigInteger('product_id');
            $table->integer('quantity');
            $table->string('observation')->nullable();
            $table->timestamps();

            $table->foreign('requirement_id')->references('id')->on('requirements')->delete('cascade');
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
        Schema::dropIfExists('requirement_details');
    }
}
