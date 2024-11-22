<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsEqualsToSalesToCreditNote extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('credit_notes', function (Blueprint $table) {
            $table->decimal('exonerated', 10, 2)->after('correlative');
            $table->decimal('unaffected', 10, 2)->after('exonerated');
            $table->decimal('free', 10, 2)->after('unaffected');
            $table->decimal('othercharge', 10, 2)->after('free');
            $table->decimal('discount', 10, 2)->after('othercharge');
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
            $table->decimal('exonerated', 10, 2)->after('correlative');
            $table->decimal('unaffected', 10, 2)->after('exonerated');
            $table->decimal('free', 10, 2)->after('unaffected');
            $table->decimal('othercharge', 10, 2)->after('free');
            $table->decimal('discount', 10, 2)->after('othercharge');
        });
    }
}
