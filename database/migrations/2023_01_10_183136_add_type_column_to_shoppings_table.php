<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTypeColumnToShoppingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shoppings', function (Blueprint $table) {
            $table->char('type', 1)->default(1);
            $table->decimal('total_retention', 10, 2)->nullable();
            $table->char('has_retention', 1)->default(0);
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
            $table->dropColumn('type');
            $table->dropColumn('total_retention');
            $table->dropColumn('has_retention');
        });
    }
}
