<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToShoppings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shoppings', function (Blueprint $table) {
            $table->decimal('igv',10,2)->nullable()->after('correlative');
            $table->string('shopping_serie',20)->nullable()->after('total');
            $table->string('shopping_correlative',20)->nullable()->after('shopping_serie');
            $table->char('shopping_type')->nullable()->after('shopping_correlative');
            $table->char('shipping_register')->nullable()->after('shopping_type');
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
            $table->string('shopping_serie',20)->nullable();
            $table->string('shopping_correlative',20)->nullable();
            $table->char('shopping_type')->nullable();
            $table->char('shipping_register')->nullable();
        });
    }
}
