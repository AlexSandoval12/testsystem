<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldPurchaseExistenceIdInPurchaseMovementsDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_movements_details', function (Blueprint $table) {
            $table->unsignedInteger('purchases_existence_id')->nullable()->after('purchase_movement_id');
            $table->foreign('purchases_existence_id')->references('id')->on('purchases_existences');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_movements_details', function (Blueprint $table) {
            $table->dropForeign(['purchases_existence_id']);
            $table->dropColumn('purchases_existence_id');
        });
    }
}
