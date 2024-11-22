<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDebitNoteDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('debit_note_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->decimal('quantity',10,2);
            $table->decimal('total',10,2);
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('debit_note_id');

            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('debit_note_id')->references('id')->on('debit_notes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('debit_note_details');
    }
}
