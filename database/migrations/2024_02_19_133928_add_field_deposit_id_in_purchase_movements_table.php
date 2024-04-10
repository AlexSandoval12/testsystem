<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldDepositIdInPurchaseMovementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_movements', function (Blueprint $table) {
            $table->unsignedBigInteger('deposit_id')->nullable()->after('branch_id');
            $table->foreign('deposit_id')->references('id')->on('deposits');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_movements', function (Blueprint $table) {
            $table->dropForeign(['deposit_id']);
            $table->dropColumn('deposit_id');
        });
    }
}
