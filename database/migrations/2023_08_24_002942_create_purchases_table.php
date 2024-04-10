<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->increments('id');
            $table->date('date');

            $table->unsignedTinyInteger('type');

            $table->unsignedInteger('branch_id');
            $table->foreign('branch_id')->references('id')->on('branches');

            $table->unsignedTinyInteger('condition');
            $table->string('number');

            $table->string('stamped')->nullable();

            $table->unsignedInteger('provider_id');
            $table->foreign('provider_id')->references('id')->on('providers');

            $table->string('razon_social');
            $table->string('ruc');
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->longText('observation')->nullable();
            $table->decimal('amount',11,2);
            $table->decimal('total_excenta',11,2)->nullable();
            $table->decimal('total_iva5',11,2)->nullable();
            $table->decimal('total_iva10',11,2)->nullable();
            $table->decimal('amount_iva5',11,2)->nullable();
            $table->decimal('amount_iva10',11,2)->nullable();
            $table->boolean('status')->default(true);

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');

            $table->datetime('date_deleted')->nullable();
            $table->string('reason_deleted')->nullable();

            $table->unsignedBigInteger('user_deleted')->nullable();
            $table->foreign('user_deleted')->references('id')->on('users');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchases');
    }
}
