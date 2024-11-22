<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePerceptionDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('perception_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('coin');
            $table->decimal('no_perceived',10,2);
            $table->integer('dues')->default(1);
            $table->integer('payment_number')->default(1);
            $table->decimal('amount_received',10,2);
            $table->decimal('amount_charged',10,2);
            $table->integer('line_modify')->nullable();
            $table->unsignedBigInteger('perception_id');
            $table->unsignedBigInteger('sale_id');

            $table->foreign('perception_id')->references('id')->on('perceptions');
            $table->foreign('sale_id')->references('id')->on('sales');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('perception_details');
    }
}
