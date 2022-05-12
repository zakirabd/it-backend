<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentExamQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_exam_questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_exam_id')->unsigned();
            $table->foreign('student_exam_id')->references('id')->on('student_exams')->onDelete('cascade');
            $table->integer('question_id');
            $table->string('title');
            $table->string('question_type');
            $table->string('sub_type')->nullable();
            $table->integer('parent_id')->nullable();
            $table->integer('sort_id')->nullable();
            $table->double('question_score')->default(0);
            $table->text('question_description')->nullable();
            $table->text('description')->nullable();
            $table->string('question_image')->nullable();
            $table->string('audio_file')->nullable();
            $table->string('video_link')->nullable();
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
        Schema::dropIfExists('student_exam_questions');
    }
}
