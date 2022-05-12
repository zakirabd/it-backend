<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentFormTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_form', function (Blueprint $table) {
            $table->id();
            $table->string('company_id');
            $table->string('date');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('date_of_birth');
            $table->string('email');
            $table->string('phone_number');
            $table->string('parent_first_name');
            $table->string('parent_last_name');
            $table->string('parent_date_of_birth');
            $table->string('parent_email');
            $table->string('current_education');
            $table->string('education_center');
            $table->string('class_course');
            $table->string('faculty');
            $table->string('specialty');
            $table->string('gpa');
            $table->string('language_certification');
            $table->string('education_type');
            $table->string('country');
            $table->string('next_specialty');
            $table->string('budget');
            $table->string('source');
            $table->string('other_source');
            $table->string('education_financing');
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
        Schema::dropIfExists('student_form');
    }
}
