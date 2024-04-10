<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangePurchaseOrderDetailIdInPurchaseMovementsDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_movements_details', function (Blueprint $table) {
            $table->dropForeign(['purchases_order_detail_id']);

            // Luego cambiamos la columna a nullable
            $table->unsignedInteger('purchases_order_detail_id')->nullable()->change();
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
            // Primero revertimos el cambio, quitando el nullable
            $table->unsignedInteger('purchases_order_detail_id')->nullable(false)->change();

            // Luego volvemos a agregar la clave foránea
            $table->foreign('purchases_order_detail_id')->references('id')->on('purchase_order_details');
        });
    }
}
