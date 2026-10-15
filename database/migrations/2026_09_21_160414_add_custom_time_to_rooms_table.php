<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            // Custom time override per soal yang bisa diset guru saat buat room (dalam detik)
            $table->unsignedInteger('custom_time_per_question')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn('custom_time_per_question');
        });
    }
};
