<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLowCommunicationDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('low_communication_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('motive', 255);
            $table->timestamps();
            $table->unsignedBigInteger('debit_note_id')->nullable();
            $table->unsignedBigInteger('credit_note_id')->nullable();
            $table->unsignedBigInteger('sale_id')->nullable();
            $table->unsignedBigInteger('low_communication_id')->nullable();

            $table->foreign('debit_note_id')->references('id')->on('debit_notes');
            $table->foreign('credit_note_id')->references('id')->on('credit_notes');
            $table->foreign('sale_id')->references('id')->on('sales');
            $table->foreign('low_communication_id')->references('id')->on('low_communications');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('low_communication_details');
    }
}
