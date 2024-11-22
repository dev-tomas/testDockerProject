<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLowToDebitNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('debit_notes', function (Blueprint $table) {
            $table->unsignedBigInteger('low_communication_id')->nullable();

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
        Schema::table('debit_notes', function (Blueprint $table) {
            //
        });
    }
}
