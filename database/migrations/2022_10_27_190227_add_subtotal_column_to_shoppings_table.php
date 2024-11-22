<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSubtotalColumnToShoppingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shoppings', function (Blueprint $table) {
            $table->decimal('subtotal', 10, 2)->default(0)->after('correlative');
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
            $table->dropColumn('subtotal');
        });
    }
}
