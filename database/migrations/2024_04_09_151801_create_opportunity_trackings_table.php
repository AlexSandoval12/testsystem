<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOpportunityTrackingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('opportunity_trackings', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('opportunity_id');
            $table->foreign('opportunity_id')->references('id')->on('opportunities');

            $table->integer('attended')->nullable();
            $table->integer('contact_form')->nullable();
            $table->integer('not_attended')->nullable();
            $table->date('call_again')->nullable();
            $table->integer('reassigned')->nullable();
            $table->boolean('closer')->nullable();
            $table->boolean('sold')->nullable();
            $table->boolean('reject')->nullable();
            $table->integer('action')->nullable();
            $table->boolean('scheduled');
            $table->integer('status');

            $table->longtext('observation')->nullable();

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');

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
        Schema::dropIfExists('opportunity_trackings');
    }
}
