<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewColumnsToShoppingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shoppings', function (Blueprint $table) {
            $table->decimal('exonerated', 10,2)->nullable()->after('correlative');
            $table->decimal('unaffected', 10,2)->nullable()->after('exonerated');
            $table->decimal('taxed', 10,2)->nullable()->after('unaffected');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shoppings', function (Blueprint $table) {
            $table->dropColumn('exonerated');
            $table->dropColumn('unaffected');
            $table->dropColumn('taxed');
        });
    }
}
