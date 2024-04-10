<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddDentalOfficeIdInDoctorScheduleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
   public function up()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Schema::table('doctor_schedules', function (Blueprint $table) {
            $table->unsignedInteger('office_id')->after('doctor_id');
            $table->foreign('office_id')->references('id')->on('offices');
        });

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('doctor_schedules', function(Blueprint $table){
            $table->dropForeign(['office_id']);
            $table->dropColumn('office_id');
        });
    }
}
