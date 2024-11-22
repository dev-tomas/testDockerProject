<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('date');
            $table->char('serialnumber', 4);
            $table->string('correlative', 10);
            $table->decimal('exonerated', 10, 2);
            $table->decimal('unaffected', 10, 2);
            $table->decimal('taxed', 10, 2);
            $table->decimal('igv', 10, 2);
            $table->decimal('free', 10, 2);
            $table->decimal('othercharge', 10, 2);
            $table->decimal('discount', 10, 2);
            $table->decimal('total', 10, 2);
            $table->boolean('status');
            $table->boolean('sendemail')->default(0);
            $table->date('issue');
            $table->date('expiration');
            $table->timestamps();
            $table->unsignedBigInteger('coin_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('typevoucher_id');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('headquarter_id');
            $table->unsignedBigInteger('quotation_id')->nullable();

            $table->foreign('coin_id')->references('id')->on('coins');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('typevoucher_id')->references('id')->on('typevouchers');
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('headquarter_id')->references('id')->on('headquarters');
            $table->foreign('quotation_id')->references('id')->on('quotations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales');
    }
}
