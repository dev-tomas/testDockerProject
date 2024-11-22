<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCorrelativesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('correlatives', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('serialnumber', 6);
            $table->string('correlative', 11);
            $table->unsignedBigInteger('headquarter_id');
            $table->unsignedBigInteger('typevoucher_id');

            $table->foreign('headquarter_id')->references('id')->on('headquarters');
            $table->foreign('typevoucher_id')->references('id')->on('typevouchers');

            // $table->unique(['serialnumber', 'typevoucher_id', 'headquarter_id', 'contingency']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('correlatives');
    }
}
