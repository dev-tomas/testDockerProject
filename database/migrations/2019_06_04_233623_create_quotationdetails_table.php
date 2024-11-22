<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuotationdetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotationdetails', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->decimal('price');
            $table->integer('unity');
            $table->decimal('igv');
            $table->decimal('total');
            $table->integer('availability')->nullable();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('quotation_id');

            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('quotation_id')->references('id')->on('quotations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quotationdetails');
    }
}
