<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExamIdColumnInAdvanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('advanceds', function (Blueprint $table) {
            $table->unsignedBigInteger('exam_id')->after('student_id')->nullable();
            $table->foreign('exam_id')->on('student_exams')->references('id')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('advanceds', function (Blueprint $table) {
            $table->dropColumn('exam_id');
        });
    }
}
