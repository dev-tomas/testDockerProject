<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPriceListIdToProductPriceListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_price_lists', function (Blueprint $table) {
            $table->dropColumn('description');
            $table->decimal('utility_percentage', 10, 2);
            $table->unsignedBigInteger('price_list_id');
            $table->foreign('price_list_id')->references('id')->on('price_lists');
            $table->index(['price_list_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_price_lists', function (Blueprint $table) {
            //
        });
    }
}
