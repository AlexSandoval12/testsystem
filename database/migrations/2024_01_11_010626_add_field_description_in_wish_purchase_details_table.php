<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldDescriptionInWishPurchaseDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wish_purchase_details', function (Blueprint $table) {
            $table->string('description')->nullable()->after('articulo_id');
            $table->integer('presentation')->nullable()->after('description');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wish_purchase_details', function (Blueprint $table) {
            $table->dropColumn('description');
            $table->dropColumn('presentation');
        });
    }
}
