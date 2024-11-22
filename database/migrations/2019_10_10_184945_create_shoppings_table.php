<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShoppingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shoppings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->dateTime('date');
            $table->decimal('exchange_rate',10,2)->nullable();
            $table->string('serial',30);
            $table->string('correlative',30);
            $table->decimal('total',10,2);
            $table->unsignedBigInteger('provider_id');
            $table->unsignedBigInteger('coin_id');
            $table->unsignedBigInteger('type_vouchers_id');
            $table->unsignedBigInteger('headquarter_id');
            $table->timestamps();
            $table->foreign('provider_id')->references('id')->on('providers');
            $table->foreign('coin_id')->references('id')->on('coins');
            $table->foreign('type_vouchers_id')->references('id')->on('typevouchers');
            $table->foreign('headquarter_id')->references('id')->on('headquarters');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shoppings');
    }
}
