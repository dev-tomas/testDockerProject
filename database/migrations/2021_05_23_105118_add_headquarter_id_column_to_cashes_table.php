<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddHeadquarterIdColumnToCashesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cashes', function (Blueprint $table) {
            $table->unsignedBigInteger('headquarter_id')->nullable();
            $table->foreign('headquarter_id')->references('id')->on('headquarters');
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
            $table->dropForeign(['headquarter_id']);
            $table->dropColumn('headquarter_id');
        });
    }
}
