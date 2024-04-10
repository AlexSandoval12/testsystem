<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCalendarPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calendar_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->date('date');
            
            $table->unsignedInteger('purchase_id')->nullable();
            $table->foreign('purchase_id')->references('id')->on('purchases');
            $table->unsignedInteger('provider_id')->nullable();
            $table->foreign('provider_id')->references('id')->on('providers');
            
            $table->longText('description');
            $table->decimal('amount', 15, 2)->default(0);

            $table->unsignedInteger('last_calendar_payment_id')->nullable();
            $table->foreign('last_calendar_payment_id')->references('id')->on('calendar_payments');

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');

            $table->unsignedBigInteger('user_rescheduled_id')->nullable();
            $table->foreign('user_rescheduled_id')->references('id')->on('users');

            $table->unsignedBigInteger('user_delete_id')->nullable();
            $table->foreign('user_delete_id')->references('id')->on('users');

            $table->longText('reason')->nullable();
            $table->integer('status')->default(1);
            
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
        Schema::dropIfExists('calendar_payments');
    }
}
