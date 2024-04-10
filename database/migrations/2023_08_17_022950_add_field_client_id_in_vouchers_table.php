<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddFieldClientIdInVouchersTable extends Migration
{
    public function up()
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->unsignedInteger('client_id')->after('expiration');
            $table->foreign('client_id')->references('id')->on('clients');
        });
    }

    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropForeign('client_id');
            $table->dropColumn('client_id');
        });

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
