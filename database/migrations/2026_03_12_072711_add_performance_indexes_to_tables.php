<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // expenses: indexes on FK columns used in WHERE clauses
        Schema::table('expenses', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('group_id');
            $table->index('category_id');
            $table->index('paid_by');
            // composite index for dashboard: WHERE user_id=? AND group_id IS NULL AND is_settled=?
            $table->index(['user_id', 'group_id', 'is_settled']);
        });

        // groups: index on created_by FK
        Schema::table('groups', function (Blueprint $table) {
            $table->index('created_by');
        });

        // expense_splits: separate index on user_id
        // (unique constraint covers (expense_id, user_id) but not user_id alone)
        Schema::table('expense_splits', function (Blueprint $table) {
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['group_id']);
            $table->dropIndex(['category_id']);
            $table->dropIndex(['paid_by']);
            $table->dropIndex(['user_id', 'group_id', 'is_settled']);
        });

        Schema::table('groups', function (Blueprint $table) {
            $table->dropIndex(['created_by']);
        });

        Schema::table('expense_splits', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
        });
    }
};
