<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseCreditTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_credit', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('date');
            $table->decimal('total', 10,1);
            $table->bigInteger('quotes');
            $table->char('status', 1)->default(0);
            $table->date('expiration');
            $table->decimal('debt');
            $table->unsignedBigInteger('purchase_id');
            $table->unsignedBigInteger('client_id')->nullable();
            $table->unsignedBigInteger('provider_id')->nullable();
            $table->timestamps();
            
            $table->foreign('purchase_id')->references('id')->on('shoppings');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('provider_id')->references('id')->on('providers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('purchase_credit');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
