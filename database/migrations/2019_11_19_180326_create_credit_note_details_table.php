<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCreditNoteDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('credit_note_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->decimal('quantity',10,2);
            $table->decimal('total',10,2);
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('credit_note_id');

            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('credit_note_id')->references('id')->on('credit_notes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('credit_note_details');
    }
}
