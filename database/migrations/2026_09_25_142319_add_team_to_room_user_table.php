<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('room_user', function (Blueprint $table) {
            $table->string('team')->nullable()->after('user_id'); // e.g. 'Red', 'Blue'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('room_user', function (Blueprint $table) {
            $table->dropColumn('team');
        });
    }
};
