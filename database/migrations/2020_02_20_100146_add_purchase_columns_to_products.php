<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPurchaseColumnsToProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('quantity_purchase',10,2)->nullable();
            $table->decimal('quantity_unit_purchase',10,2)->nullable();
            $table->unsignedBigInteger('operation_type_purchase')->nullable();
            $table->foreign('operation_type_purchase')->references('id')->on('operations_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
}
