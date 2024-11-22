<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTypeThemeColumnToUsersInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users_info', function (Blueprint $table) {
            $table->char('type_theme', 1)->after('theme')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users_info', function (Blueprint $table) {
            $table->char('type_theme', 1)->after('theme')->default(0);
        });
    }
}
