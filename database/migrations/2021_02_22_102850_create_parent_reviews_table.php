<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParentReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parent_reviews', function (Blueprint $table) {
            $table->id();
            $table->integer('rating')->default(0);
            $table->string('note')->default(0);

            $table->unsignedBigInteger('teacher_id');
            $table->foreign('teacher_id')->on('users')->references('id')->onDelete('cascade');

            $table->unsignedBigInteger('user_id')->comment('parent id on users table');
            $table->foreign('user_id')->on('users')->references('id')->onDelete('cascade');
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
        Schema::dropIfExists('parent_reviews');
    }
}
