<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToShoppingDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shopping_details', function (Blueprint $table) {
            $table->decimal('igv', 10,2)->nullable()->after('subtotal');
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
            $table->decimal('igv', 10,2)->nullable()->after('subtotal');
        });
    }
}
