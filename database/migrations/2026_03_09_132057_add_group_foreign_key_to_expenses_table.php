<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Existing databases already have this FK from the original expenses migration.
        if ($this->hasGroupForeignKey()) {
            return;
        }

        Schema::table('expenses', function (Blueprint $table) {
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        if (! $this->hasGroupForeignKey()) {
            return;
        }

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign(['group_id']);
        });
    }

    private function hasGroupForeignKey(): bool
    {
        foreach (Schema::getForeignKeys('expenses') as $foreignKey) {
            if ($foreignKey['columns'] === ['group_id']) {
                return true;
            }
        }

        return false;
    }
};
