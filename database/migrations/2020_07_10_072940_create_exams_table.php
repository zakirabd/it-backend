<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exams', function (Blueprint $table) {

            $table->id();
            $table->string('title');
            $table->integer('duration_minutes')->nullable();
            $table->integer('retake_minutes')->default(0);
            $table->integer('retake_time')->default(0);
            $table->integer('points')->nullable();
            $table->text('description')->nullable();
            $table->string('exam_image')->nullable();
            $table->unsignedBigInteger('course_id');
            $table->foreign('course_id')->on('courses')->references('id')->onDelete('cascade');
            $table->unsignedBigInteger('lesson_id');
            $table->foreign('lesson_id')->on('lessons')->references('id')->onDelete('cascade');
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
        Schema::dropIfExists('exams');
    }
}
