<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCashesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cashes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('ip');
            $table->decimal('exchange_type', 10,3);
            $table->decimal('opening_amount', 10,2)->nullable();
            $table->decimal('closing_amount', 10,2)->nullable();
            $table->char('status', 1)->default(0);
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('coin_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('user_create');
            $table->unsignedBigInteger('user_update');
            $table->timestamps();

            $table->foreign('user_update')->references('id')->on('users');
            $table->foreign('user_create')->references('id')->on('users');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('coin_id')->references('id')->on('coins');
            $table->foreign('client_id')->references('id')->on('clients');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cashes');
    }
}
