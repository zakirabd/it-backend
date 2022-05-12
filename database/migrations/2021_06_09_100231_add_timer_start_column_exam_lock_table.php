<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTimerStartColumnExamLockTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('exam_locked', function (Blueprint $table) {
            $table->dateTime('timer_start')->nullable()->after('retake_count');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('exam_locked', function (Blueprint $table) {
            $table->dropColumn('timer_start');
        });
    }
}
