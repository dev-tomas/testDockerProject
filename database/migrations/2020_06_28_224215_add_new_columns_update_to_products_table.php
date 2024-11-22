<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewColumnsUpdateToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('image', 250)->default('default.jpg');
            $table->string('detail', 250)->nullable();
            $table->string('external_code', 50)->nullable();
            $table->string('equivalence_code', 50)->nullable();
            $table->unsignedBigInteger('classification_id')->nullable();

            $table->foreign('classification_id')->references('id')->on('classifications');
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
