<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCashesMovementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cashes_movements', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('movement');
            $table->decimal('amount', 10,2);
            $table->string('document', 50)->nullable();
            $table->text('observation')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('cash_id');
            $table->timestamps();

            $table->foreign('cash_id')->references('id')->on('cashes');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cashes_movements');
    }
}
