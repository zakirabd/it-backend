<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentsLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students_log', function (Blueprint $table) {
            $table->id();
            $table->string('student_id');
            $table->string('enroll_type');
            $table->string('type');
            $table->string('course_id')->nullable();
            $table->string('teacher_id')->nullable();
            $table->string('lesson_mode')->nullable();
            $table->string('study_mode')->nullable();
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
        Schema::dropIfExists('students_log');
    }
}
