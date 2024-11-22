<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyCachesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cashes', function (Blueprint $table) {
            $table->string('code')->nullable()->after('ip');
            $table->string('account')->nullable()->after('code');
            $table->string('ip', 120)->nullable()->change();
            $table->decimal('exchange_type', 10,3)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cashes', function (Blueprint $table) {
            //
        });
    }
}
