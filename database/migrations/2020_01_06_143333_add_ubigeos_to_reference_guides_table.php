<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUbigeosToReferenceGuidesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reference_guides', function (Blueprint $table) {
            $table->unsignedBigInteger('arrival_address_ubigeo')->nullable();
            $table->unsignedBigInteger('start_address_ubigeo')->nullable();

            $table->foreign('arrival_address_ubigeo')->references('id')->on('ubigeos');
            $table->foreign('start_address_ubigeo')->references('id')->on('ubigeos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reference_guides', function (Blueprint $table) {
            //
        });
    }
}
