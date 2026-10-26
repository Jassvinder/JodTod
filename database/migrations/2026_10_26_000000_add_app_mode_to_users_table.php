<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Which part of the app the user works in: the personal expense/task app or Team Builder.
     * Null means the user has not chosen yet, so the app asks once.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('app_mode', ['personal', 'team_builder'])->nullable()->after('is_guest');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('app_mode');
        });
    }
};
