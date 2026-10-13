<?php

use App\Models\Category;
use Illuminate\Database\Migrations\Migration;

/*
 * Production deploys run only `migrate` (no seeders), which left the categories
 * table empty and blocked adding expenses. Insert the defaults as part of migrating.
 */
return new class extends Migration
{
    public function up(): void
    {
        Category::ensureDefaults();
    }

    public function down(): void
    {
        // Default categories may already be referenced by expenses, so they are kept.
    }
};
