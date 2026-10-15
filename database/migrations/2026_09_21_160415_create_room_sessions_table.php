<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->unique()->constrained()->cascadeOnDelete();
            $table->enum('status', ['waiting', 'playing', 'finished'])->default('waiting');
            $table->unsignedInteger('current_question_index')->default(0);
            $table->timestamp('question_started_at')->nullable();
            // Override waktu khusus untuk soal saat ini (null = pakai default game/room)
            $table->unsignedInteger('current_time_limit')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_sessions');
    }
};
