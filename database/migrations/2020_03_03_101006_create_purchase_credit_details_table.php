<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseCreditDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_credit_payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('date')->nullable();
            $table->date('expiration');
            $table->bigInteger('quote');
            $table->string('payment_type')->nullable();
            $table->decimal('payment', 10,2);
            $table->char('paid', 1)->default(0);
            $table->string('bank')->nullable();
            $table->string('operation_bank')->nullable();
            $table->unsignedBigInteger('purchase_credit_id')->nullable();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->timestamps();

            $table->foreign('purchase_credit_id')->references('id')->on('purchase_credit')->onDelete('cascade');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
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
        Schema::dropIfExists('purchase_credit_payments');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
