<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('remember_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('note', 255)->nullable();
            $table->timestamps();

            $table->index(['user_id', 'name']);
        });

        Schema::create('remember_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('remember_item_id')->constrained()->cascadeOnDelete();
            $table->date('done_on');
            $table->string('note', 255)->nullable();
            $table->timestamps();

            $table->index(['remember_item_id', 'done_on']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('remember_entries');
        Schema::dropIfExists('remember_items');
    }
};
