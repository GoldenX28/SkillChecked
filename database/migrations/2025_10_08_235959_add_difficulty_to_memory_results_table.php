<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('memory_results', function (Blueprint $table) {
            $table->string('difficulty')->default('normal')->after('time_seconds');
        });
    }

    public function down()
    {
        Schema::table('memory_results', function (Blueprint $table) {
            $table->dropColumn('difficulty');
        });
    }
};
