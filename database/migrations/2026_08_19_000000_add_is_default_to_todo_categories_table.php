<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('todo_categories', function (Blueprint $table) {
            $table->boolean('is_default')->default(false)->after('color');
        });

        DB::table('todo_categories')
            ->whereIn('name', ['Home', 'Market'])
            ->update(['is_default' => true]);
    }

    public function down(): void
    {
        Schema::table('todo_categories', function (Blueprint $table) {
            $table->dropColumn('is_default');
        });
    }
};
