<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->unsignedBigInteger('exam_id');
            $table->foreign('exam_id')->on('exams')->references('id')->onDelete('cascade');
            $table->text('description')->nullable();
            $table->text('question_description')->nullable();
            $table->string('type');
            $table->string('sub_type')->nullable();
            $table->integer('score')->default('0');
            $table->text('question_image')->nullable();
            $table->text('audio_file')->nullable();
            $table->text('video_link')->nullable();
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
        Schema::dropIfExists('questions');
    }
}
