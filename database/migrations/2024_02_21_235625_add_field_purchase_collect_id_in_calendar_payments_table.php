<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldPurchaseCollectIdInCalendarPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('calendar_payments', function (Blueprint $table) {
            $table->unsignedBigInteger('purchase_collect_id')->nullable()->after('provider_id');
            $table->foreign('purchase_collect_id')->references('id')->on('purchases_collects')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('calendar_payments', function (Blueprint $table) {
            $table->dropForeign(['purchase_collect_id']);
            $table->dropColumn('purchase_collect_id');
        });
    }
}
