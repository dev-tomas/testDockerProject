<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsSunatToClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('trade_name', 150);
            $table->string('business_name', 150);
            $table->string('address', 150);
            $table->string('logo', 40)->default('default.jpg');
            $table->string('phone', 40)->nullable();
            $table->string('phone2', 40)->nullable();
            $table->string('email', 50)->nullable();
            $table->string('web', 60)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clients', function (Blueprint $table) {
            //
        });
    }
}
