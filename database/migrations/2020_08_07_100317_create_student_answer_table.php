<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentAnswerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_exam_question_id')->unsigned()->nullable();;
            $table->foreign('student_exam_question_id')->references('id')->on('student_exam_questions')->onDelete('cascade');
            $table->unsignedBigInteger('answer_id')->unsigned()->nullable();
            $table->foreign('answer_id')->references('id')->on('student_exam_question_answers')->onDelete('cascade');
            $table->integer('matching_answer_id')->nullable();;
            $table->string('answer');
            $table->tinyInteger('is_correct')->default(0);
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
        Schema::dropIfExists('student_answer');
    }
}
