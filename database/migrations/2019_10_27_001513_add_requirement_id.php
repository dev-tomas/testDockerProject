<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRequirementId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('providers_quotations', function (Blueprint $table) {
            $table->unsignedBigInteger('requirement_id')->after('client_id');
            $table->foreign('requirement_id')->references('id')->on('requirements');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('providers_quotations', function (Blueprint $table) {
            $table->unsignedBigInteger('requirement_id');
        });
    }
}
