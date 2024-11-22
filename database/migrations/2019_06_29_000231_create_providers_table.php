<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProvidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('providers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('description', 150);
            $table->string('document', 15);
            $table->string('phone', 12)->nullable();
            $table->string('address', 150)->nullable();
            $table->string('email', 50)->nullable();
            $table->string('secondary_email', 50)->nullable();
            $table->string('tradename', 50);
            $table->string('detraction', 50);
            $table->unsignedBigInteger('typedocument_id');
            $table->unsignedBigInteger('client_id');
            $table->timestamps();

            $table->foreign('typedocument_id')->references('id')->on('typedocuments');
            $table->foreign('client_id')->references('id')->on('clients');

            $table->unique(['document', 'client_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('providers');
    }
}
