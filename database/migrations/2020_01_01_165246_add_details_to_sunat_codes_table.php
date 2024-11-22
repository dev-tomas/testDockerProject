<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDetailsToSunatCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sunat_codes', function (Blueprint $table) {
            $table->string('code',4)->change();
            $table->string('detail', 40)->nullable();
            $table->string('what_to_do', 40)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sunat_codes', function (Blueprint $table) {
            //
        });
    }
}
