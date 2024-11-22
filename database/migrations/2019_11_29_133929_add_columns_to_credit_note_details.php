<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToCreditNoteDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('credit_note_details', function (Blueprint $table) {
            $table->decimal('price', 10,2)->nullable()->after('quantity');
            $table->decimal('subtotal', 10,2)->nullable()->after('price');
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
            $table->decimal('price', 10,2)->nullable();
            $table->decimal('subtotal', 10,2)->nullable();
        });
    }
}
