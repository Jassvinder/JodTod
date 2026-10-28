<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Team Builder phase 3: weekly targets and team announcements.
     */
    public function up(): void
    {
        // One weekly target per user, set by the user or by anyone above them in a team.
        Schema::create('team_builder_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('set_by')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedSmallInteger('plans_per_week')->default(0);
            $table->unsignedSmallInteger('follow_ups_per_week')->default(0);
            $table->timestamps();
        });

        // A message from a member to everyone below them in the team (training links, meeting times).
        Schema::create('team_announcements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('message', 1000);
            $table->string('link', 500)->nullable();
            $table->timestamps();

            $table->index(['team_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_announcements');
        Schema::dropIfExists('team_builder_targets');
    }
};
