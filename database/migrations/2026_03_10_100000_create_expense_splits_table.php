<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expense_splits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expense_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('share_amount', 12, 2);
            $table->decimal('percentage', 5, 2)->nullable();
            $table->boolean('is_settled')->default(false);
            $table->timestamps();

            $table->unique(['expense_id', 'user_id']);
            $table->index('is_settled');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expense_splits');
    }
};
