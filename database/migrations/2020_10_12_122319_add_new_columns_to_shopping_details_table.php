<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewColumnsToShoppingDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shopping_details', function (Blueprint $table) {
            $table->char('type_purchase', 1)->nullable();
            $table->unsignedBigInteger('warehouse_id')->nullable();
            $table->unsignedBigInteger('center_cost_id')->nullable();
            $table->foreign('warehouse_id')->references('id')->on('warehouses');
            $table->foreign('center_cost_id')->references('id')->on('costs_center');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shopping_details', function (Blueprint $table) {
            $table->dropColumn('type_purchase');
            $table->dropForeign(['warehouse_id','center_cost_id' ]);
            $table->dropColumn('warehouse_id');
            $table->dropColumn('center_cost_id');
        });
    }
}
