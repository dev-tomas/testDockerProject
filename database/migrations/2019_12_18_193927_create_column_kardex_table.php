<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateColumnKardexTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kardex', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('type_transaction');
            $table->string('number')->nullable();
            $table->decimal('entry', 10,5)->nullable();
            $table->decimal('output', 10,5)->nullable();
            $table->decimal('balance', 10,5)->nullable();
            $table->unsignedBigInteger('headquarter_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('client_id');
            $table->timestamps();

            $table->foreign('client_id')->references('id')->on('clients');
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('headquarter_id')->references('id')->on('headquarters');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kardex');
    }
}
