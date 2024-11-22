<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnKardex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kardex', function (Blueprint $table) {
            $table->dropForeign(['headquarter_id']);
            $table->dropColumn('headquarter_id');
            $table->unsignedBigInteger('warehouse_id')->after('balance');
            $table->foreign('warehouse_id')->references('id')->on('warehouses');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kardex', function (Blueprint $table) {
            $table->unsignedBigInteger('warehouse_id')->after('balance');
        });
    }
}
