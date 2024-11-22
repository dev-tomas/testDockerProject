<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRetentionDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('retention_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('coin');
            $table->decimal('no_retention',10,2);
            $table->integer('dues')->default(1);
            $table->integer('payment_number')->default(1);
            $table->decimal('retained_amount',10,2);
            $table->decimal('amount_paid',10,2);
            $table->integer('line_modify')->nullable();
            $table->unsignedBigInteger('retention_id');

            $table->foreign('retention_id')->references('id')->on('retentions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('retention_details');
    }
}
