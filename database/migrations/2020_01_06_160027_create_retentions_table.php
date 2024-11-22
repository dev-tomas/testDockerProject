<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRetentionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('retentions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('serial_number', 15);
            $table->string('correlative', 15);
            $table->date('issue');
            $table->string('coin', 20);
            $table->string('observation', 255)->nullable();
            $table->decimal('retained_amount',10,2);
            $table->decimal('amount_paid',10,2);
            $table->decimal('amount',10,2);
            $table->integer('exchange_factor')->nullable();
            $table->string('exchange_obj',10)->nullable();
            $table->string('exchange_ref',10)->nullable();
            $table->boolean('status_sunat')->default(0);
            $table->boolean('status')->default(1);
            $table->timestamps();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('regime_id');
            $table->unsignedBigInteger('user_created');
            $table->unsignedBigInteger('user_updated');
            $table->unsignedBigInteger('response_sunat')->nullable();
            $table->unsignedBigInteger('typevoucher_id');
            $table->unsignedBigInteger('headquarter_id');

            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('regime_id')->references('id')->on('regimes');
            $table->foreign('user_created')->references('id')->on('users');
            $table->foreign('user_updated')->references('id')->on('users');
            $table->foreign('response_sunat')->references('id')->on('sunat_codes');
            $table->foreign('typevoucher_id')->references('id')->on('typevouchers');
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
        Schema::dropIfExists('retentions');
    }
}
