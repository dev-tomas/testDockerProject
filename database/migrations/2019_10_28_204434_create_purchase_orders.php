<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('serie');
            $table->string('correlative');
            $table->string('delivery_term');
            $table->string('condition');
            $table->string('delivery');
            $table->decimal('igv', 10,2);
            $table->decimal('investment', 10,2);
            $table->char('status');
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('providerquotation_id');
            $table->unsignedBigInteger('typevoucher_id');
            $table->timestamps();

            $table->foreign('providerquotation_id')->references('id')->on('providers_quotations');
            $table->foreign('client_id')->references('id')->on('clients');
            $table->foreign('typevoucher_id')->references('id')->on('typevouchers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return voidre
     */
    public function down()
    {
        Schema::dropIfExists('purchase_orders');
    }
}
