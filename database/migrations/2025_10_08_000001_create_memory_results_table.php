<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('memory_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedInteger('moves');
            $table->decimal('time_seconds', 8, 3);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('memory_results');
    }
};
