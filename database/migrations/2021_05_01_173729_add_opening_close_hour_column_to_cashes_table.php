<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOpeningCloseHourColumnToCashesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cashes', function (Blueprint $table) {
            $table->timestamp('opening_hour')->nullable();
            $table->timestamp('closing_hour')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cashes', function (Blueprint $table) {
            $table->dropColumn('opening_hour');
            $table->dropColumn('closing_hour');
        });
    }
}
