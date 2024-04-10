<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldArticuloIdPurchaseMovementsDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_movements_details', function (Blueprint $table) {
            $table->unsignedBigInteger('articulo_id')->nullable()->after('affects_stock');
            $table->foreign('articulo_id')->references('id')->on('articulo');
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
            //
        });
    }
}
