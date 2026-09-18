<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('friend_code', 6)->nullable()->unique()->after('avatar');
        });

        User::whereNull('friend_code')->orderBy('id')->each(function (User $user) {
            do {
                $code = User::generateFriendCode();
            } while (User::where('friend_code', $code)->exists());

            $user->forceFill(['friend_code' => $code])->saveQuietly();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('friend_code');
        });
    }
};
