<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDoctorScheduleDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('doctor_schedule_details', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('doctor_schedule_id');
            $table->foreign('doctor_schedule_id')->references('id')->on('doctor_schedules');
            $table->unsignedInteger('speciality_id');
            $table->foreign('speciality_id')->references('id')->on('especialities');
            $table->time('from_time');
            $table->time('until_time');
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
        Schema::dropIfExists('doctor_schedule_details');
    }
}
