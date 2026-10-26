<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Team Builder: network marketing teams (upline/downline), prospects and their follow-ups.
     */
    public function up(): void
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('company_name', 100)->nullable();
            $table->timestamps();
        });

        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // The member whose invite code was used to join (null for the team owner).
            $table->foreignId('sponsor_id')->nullable()->constrained('users')->nullOnDelete();
            // Each member has their own code, so joining with it makes them the sponsor.
            $table->string('invite_code', 8)->unique();
            // User ids from the top of the tree down to this member, e.g. "/3/12/45/".
            // The whole downline of a member is every row whose path starts with theirs.
            $table->string('path', 1000);
            $table->timestamp('joined_at')->useCurrent();
            $table->timestamps();

            $table->unique(['team_id', 'user_id']);
            $table->index(['team_id', 'sponsor_id']);
        });

        Schema::create('prospects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('team_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name', 100);
            $table->string('phone', 20)->nullable();
            $table->string('source', 100)->nullable();
            $table->enum('status', ['new', 'plan_shown', 'follow_up', 'joined', 'not_interested'])->default('new');
            $table->dateTime('next_follow_up_at')->nullable();
            $table->timestamp('joined_at')->nullable();
            $table->string('note', 500)->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'next_follow_up_at']);
        });

        Schema::create('prospect_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prospect_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['call', 'plan_shown', 'follow_up', 'meeting', 'note']);
            $table->string('note', 500)->nullable();
            $table->dateTime('done_at');
            $table->timestamps();

            $table->index(['prospect_id', 'done_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prospect_activities');
        Schema::dropIfExists('prospects');
        Schema::dropIfExists('team_members');
        Schema::dropIfExists('teams');
    }
};
