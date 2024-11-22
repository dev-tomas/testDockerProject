<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCreditDebitNoteColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->unsignedBigInteger('credit_note_id')->nullable();
            $table->unsignedBigInteger('debit_note_id')->nullable();

            $table->foreign('credit_note_id')->references('id')->on('credit_notes');
            $table->foreign('debit_note_id')->references('id')->on('debit_notes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->unsignedBigInteger('credit_note_id')->nullable();
            $table->unsignedBigInteger('debit_note_id')->nullable();
        });
    }
}
