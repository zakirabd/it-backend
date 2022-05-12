<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEssayReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('essay_reviews', function (Blueprint $table) {
            $table->id();
            $table->text('review');
            $table->Integer('grade')->nullable();
            $table->boolean('is_student');
            $table->timestamp('seen_at')->nullable();
            $table->unsignedBigInteger('essay_answer_id');
            $table->foreign('essay_answer_id')->references('id')->on('essay_answers')->onDelete('cascade');
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
        Schema::dropIfExists('essay_reviews');
    }
}
