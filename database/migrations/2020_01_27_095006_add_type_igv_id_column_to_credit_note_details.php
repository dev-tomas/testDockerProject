<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTypeIgvIdColumnToCreditNoteDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('credit_note_details', function (Blueprint $table) {
            $table->unsignedBigInteger('type_igv_id')->after('product_id')->nullable();
            $table->foreign('type_igv_id')->references('id')->on('igv_type');
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
            $table->unsignedBigInteger('type_igv_id')->after('product_id');
        });
    }
}