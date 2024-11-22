<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSunatToLowCommunicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('low_communications', function (Blueprint $table) {
            $table->boolean('status_sunat')->default(0);
            $table->unsignedBigInteger('sunat_code_id')->nullable();

            $table->foreign('sunat_code_id')->references('id')->on('sunat_codes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('low_communications', function (Blueprint $table) {
            //
        });
    }
}
