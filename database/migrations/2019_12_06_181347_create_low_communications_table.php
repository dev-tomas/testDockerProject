<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLowCommunicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('low_communications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('correlative', 12);
            $table->date('generation_date');
            $table->date('communication_date');
            $table->timestamps();
            $table->unsignedBigInteger('headquarter_id');

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
        Schema::dropIfExists('low_communications');
    }
}
