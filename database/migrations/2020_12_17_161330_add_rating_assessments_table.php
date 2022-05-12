<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRatingAssessmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assessments', function (Blueprint $table) {

            $table->integer('home_work')->after('note')->default(0);
            $table->integer('participation')->after('home_work')->default(0);
            $table->integer('reading_comprehension')->after('participation')->default(0);
            $table->integer('speaking_fluency')->after('reading_comprehension')->default(0);
            $table->integer('writing_skills')->after('speaking_fluency')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('assessments', function (Blueprint $table) {
            //
        });
    }
}
