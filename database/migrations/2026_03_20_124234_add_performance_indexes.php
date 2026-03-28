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
        Schema::table('expense_splits', function (Blueprint $table) {
            $table->index(['expense_id', 'user_id'], 'splits_expense_user_idx');
            $table->index(['user_id', 'is_settled'], 'splits_user_settled_idx');
        });

        Schema::table('group_members', function (Blueprint $table) {
            $table->index(['group_id', 'user_id', 'is_approved'], 'gm_group_user_approved_idx');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->index(['group_id', 'is_settled', 'deleted_at'], 'expenses_group_settled_idx');
            $table->index(['paid_by', 'is_settled', 'deleted_at'], 'expenses_payer_settled_idx');
        });
    }

    public function down(): void
    {
        Schema::table('expense_splits', function (Blueprint $table) {
            $table->dropIndex('splits_expense_user_idx');
            $table->dropIndex('splits_user_settled_idx');
        });

        Schema::table('group_members', function (Blueprint $table) {
            $table->dropIndex('gm_group_user_approved_idx');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropIndex('expenses_group_settled_idx');
            $table->dropIndex('expenses_payer_settled_idx');
        });
    }
};
