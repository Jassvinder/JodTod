<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Lend & Borrow: a simple personal ledger of money given to and received from people.
     */
    public function up(): void
    {
        Schema::create('ledger_people', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('phone', 20)->nullable();
            $table->string('note', 255)->nullable();
            $table->timestamps();

            $table->index(['user_id', 'name']);
        });

        Schema::create('ledger_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ledger_person_id')->constrained('ledger_people')->cascadeOnDelete();
            // gave = I gave money to this person, got = I received money from this person
            $table->enum('type', ['gave', 'got']);
            $table->decimal('amount', 12, 2);
            $table->date('entry_date');
            $table->string('note', 255)->nullable();
            $table->timestamps();

            $table->index(['ledger_person_id', 'entry_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ledger_entries');
        Schema::dropIfExists('ledger_people');
    }
};
