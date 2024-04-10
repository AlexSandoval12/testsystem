<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->increments('id');
            $table->date('date');
            $table->unsignedInteger('enterprise_id');
            $table->unsignedInteger('branch_id');
            $table->unsignedInteger('voucher_box_id');
            $table->unsignedTinyInteger('voucher_condition');
            $table->integer('voucher_number');
            $table->date('expiration')->nullable();
            $table->string('razon_social');
            $table->string('ruc');
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->unsignedTinyInteger('voucher_type');
            $table->longText('observation')->nullable();
            $table->decimal('amount',11,2);
            $table->decimal('total_excenta',11,2)->nullable();
            $table->decimal('total_iva5',11,2)->nullable();
            $table->decimal('total_iva10',11,2)->nullable();
            $table->decimal('amount_iva5',11,2)->nullable();
            $table->decimal('amount_iva10',11,2)->nullable();
            $table->boolean('status')->default(true);
            $table->unsignedBigInteger('user_id');
            $table->string('reason_canceled')->nullable();
            $table->unsignedBigInteger('user_canceled')->nullable();
            $table->timestamps();
            $table->string('voucher_fullnumber')->nullable();
            $table->unsignedInteger('stamped_id')->nullable();
            $table->foreign('stamped_id')->references('id')->on('stampeds');
            $table->foreign('enterprise_id')->references('id')->on('enterprises');
            $table->foreign('branch_id')->references('id')->on('branches');
            $table->foreign('voucher_box_id')->references('id')->on('voucher_boxes');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('user_canceled')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vouchers');
    }
}
