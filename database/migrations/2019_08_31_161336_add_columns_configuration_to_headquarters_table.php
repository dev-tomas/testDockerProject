<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsConfigurationToHeadquartersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('headquarters', function (Blueprint $table) {
            $table->string('address', 255)->nullable();
            $table->string('code', 30)->nullable();
            $table->unsignedBigInteger('ubigeo_id')->nullable();

            $table->foreign('ubigeo_id')->references('id')->on('ubigeos');
            $table->unique(['code', 'client_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('headquarters', function (Blueprint $table) {
            //
        });
    }
}
