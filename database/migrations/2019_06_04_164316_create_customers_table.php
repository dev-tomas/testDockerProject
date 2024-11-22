<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('description', 70);
            $table->string('document', 15);
            $table->string('phone', 12)->nullable();
            $table->text('address')->nullable();
            $table->unsignedBigInteger('typedocument_id');
            $table->unsignedBigInteger('client_id');
            $table->timestamps();

            $table->foreign('typedocument_id')->references('id')->on('typedocuments');
            $table->foreign('client_id')->references('id')->on('clients');

            $table->unique(['document', 'client_id', 'typedocument_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customers');
    }
}
