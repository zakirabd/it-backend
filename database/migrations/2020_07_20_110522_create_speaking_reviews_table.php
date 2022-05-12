<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpeakingReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('speaking_reviews', function (Blueprint $table) {
            $table->id();
            $table->text('review');
            $table->boolean('is_student');
            $table->timestamp('seen_at')->nullable();
            $table->unsignedBigInteger('speaking_answer_id');
            $table->foreign('speaking_answer_id')->references('id')->on('speaking_answer')->onDelete('cascade');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('speaking_reviews');
    }
}
