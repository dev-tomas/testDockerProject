<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewSerieNewCorrelativeColumnsToCreditNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('credit_notes', function (Blueprint $table) {
            $table->string('new_serie_related', 4)->nullable();
            $table->string('new_correlative_related', 8)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('credit_notes', function (Blueprint $table) {
            //
        });
    }
}
