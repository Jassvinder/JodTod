<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('paid_by')->constrained('users')->onDelete('cascade');
            $table->decimal('amount', 12, 2);
            $table->foreignId('category_id')->constrained()->onDelete('restrict');
            $table->string('description', 255)->nullable();
            $table->date('expense_date');
            $table->enum('split_type', ['equal', 'custom', 'percentage'])->nullable();
            $table->boolean('is_settled')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index('expense_date');
            $table->index('is_settled');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
