<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class IsClosedToSpeakingAnswer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('speaking_answer', function (Blueprint $table) {
            $table->tinyInteger('is_closed')->unsigned()->default('0')->after('status');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('speaking_answer', function (Blueprint $table) {
            $table->dropColumn('is_closed');

        });
    }
}
