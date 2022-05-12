<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddContentToLessonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->text('static_content')->nullable()->after('course_id');
            $table->string('audio_file')->nullable()->after('static_content');
            $table->string('video_link')->nullable()->after('audio_file');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropColumn('static_content');
            $table->dropColumn('audio_file');
            $table->dropColumn('video_link');
        });
    }
}
