<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBankMovementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bank_movements', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('date');
            $table->string('bank');
            $table->decimal('amount', 10, 2);
            $table->string('operation_number')->nullable();
            $table->string('description')->nullable();
            $table->string('movement_type')->nullable();
            $table->unsignedBigInteger('sale_id')->nullable();
            $table->unsignedBigInteger('shopping_id')->nullable();
            $table->unsignedBigInteger('client_id');
            $table->timestamps();

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
        Schema::dropIfExists('bank_movements');
    }
}
