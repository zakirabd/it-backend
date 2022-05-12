<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeacherScheduleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teacher_schedule', function (Blueprint $table) {
            $table->id();
            $table->string('company_id');
            $table->string('teacher_id');
            $table->string('group_id');
            $table->string('student_id');
            $table->string('study_mode');
            $table->string('time');
            $table->string('weekday');
            $table->string('start_time');
            $table->string('finish_time');
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
        Schema::dropIfExists('teacher_schedule');
    }
}
