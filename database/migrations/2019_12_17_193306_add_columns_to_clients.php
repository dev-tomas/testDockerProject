<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToClients extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->char('production', 1)->default(0)->nullable()->after('status');
            $table->date('production_at')->nullable()->after('production');
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
            $table->char('production', 1)->default(0)->nullable()->after('status');
            $table->date('production_at')->nullable()->after('production');
        });
    }
}
