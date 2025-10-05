<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('type_speed_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('wpm');
            $table->integer('correct_chars');
            $table->integer('errors');
            $table->integer('typed_chars');
            $table->integer('accuracy');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('type_speed_results');
    }
};
