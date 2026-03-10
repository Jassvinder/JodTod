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
        Schema::create('settlements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('from_user'); // debtor
            $table->unsignedBigInteger('to_user');   // creditor
            $table->decimal('amount', 12, 2);
            $table->enum('status', ['pending', 'completed'])->default('pending');
            $table->string('note', 255)->nullable();
            $table->timestamp('settled_at')->nullable();
            $table->timestamps();

            $table->foreign('from_user')->references('id')->on('users');
            $table->foreign('to_user')->references('id')->on('users');
            $table->index('group_id');
            $table->index('from_user');
            $table->index('to_user');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settlements');
    }
};
