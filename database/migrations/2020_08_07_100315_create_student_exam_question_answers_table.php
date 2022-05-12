<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentExamQuestionAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_exam_question_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_exam_question_id')->unsigned();
            $table->foreign('student_exam_question_id')->references('id')->on('student_exam_questions')->onDelete('cascade');
            $table->string('title');
            $table->string('is_correct')->default(0);
            $table->double('score')->default(0);
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
        Schema::dropIfExists('student_exam_question_answers');
    }
}
