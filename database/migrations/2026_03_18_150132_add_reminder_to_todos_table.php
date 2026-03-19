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
        Schema::table('todos', function (Blueprint $table) {
            $table->dateTime('reminder_at')->nullable()->after('due_date');
            $table->boolean('reminder_sent')->default(false)->after('reminder_at');
            $table->index('reminder_at');
        });
    }

    public function down(): void
    {
        Schema::table('todos', function (Blueprint $table) {
            $table->dropIndex(['reminder_at']);
            $table->dropColumn(['reminder_at', 'reminder_sent']);
        });
    }
};
