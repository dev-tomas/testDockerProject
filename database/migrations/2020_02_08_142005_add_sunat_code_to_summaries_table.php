<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSunatCodeToSummariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('summaries', function (Blueprint $table) {
            $table->boolean('status_sunat')->nullable()->default(0);
            $table->unsignedBigInteger('response_sunat')->nullable();

            $table->foreign('response_sunat')->references('id')->on('sunat_codes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('summaries', function (Blueprint $table) {
            //
        });
    }
}
