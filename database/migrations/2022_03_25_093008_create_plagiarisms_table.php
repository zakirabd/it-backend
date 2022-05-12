<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlagiarismsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plagiarism', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('checked_essay_id')->nullable();
            $table->foreign('checked_essay_id')->references('id')->on('essay_answers')->onDelete('cascade');
            
            $table->unsignedBigInteger('matched_essay_id')->nullable();
            $table->foreign('matched_essay_id')->references('id')->on('essay_answers')->onDelete('cascade');

            $table->string('percentage')->nullable();

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
        Schema::dropIfExists('plagiarism');
    }
}
