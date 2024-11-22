<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTaxedIgvColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('debit_notes', function (Blueprint $table) {
            $table->decimal('taxed', 10, 2)->after('correlative');
            $table->decimal('igv', 10, 2)->after('taxed');
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
            $table->decimal('taxed', 10, 2);
            $table->decimal('igv', 10, 2);
        });
    }
}
