<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIgvPercentageToCreditNoteDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('credit_note_details', function (Blueprint $table) {
            $table->decimal('igv_percentage',10,2)->nullable();
            $table->decimal('price_unit',10,2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('credit_note_details', function (Blueprint $table) {
            //
        });
    }
}
