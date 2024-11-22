<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSummariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('summaries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('correlative', 11);
            $table->string('summary', 50)->nullable();
            $table->string('ticket', 50)->nullable();
            $table->date('date_issues');
            $table->date('date_generation');
            $table->timestamps();
            $table->unsignedBigInteger('headquarter_id');
            $table->unsignedBigInteger('user_id');

            $table->foreign('headquarter_id')->references('id')->on('headquarters');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('summaries');
    }
}
