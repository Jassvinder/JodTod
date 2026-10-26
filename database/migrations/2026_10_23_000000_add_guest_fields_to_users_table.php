<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Guest users are group members without the app: a name only, no email,
     * no password, never notified and unable to log in.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable()->change();
            $table->boolean('is_guest')->default(false)->after('role');
            $table->foreignId('guest_created_by')->nullable()->after('is_guest')
                ->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('guest_created_by');
            $table->dropColumn('is_guest');
        });
    }
};
