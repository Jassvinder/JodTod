<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Jasvinder Kumar',
                'email' => 'info@jodtod.com',
                'phone' => '9416716775',
                'phone_verified_at' => now(),
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => 'admin',
            ],
            [
                'name' => 'Mohan Lal',
                'email' => 'mohan@example.com',
                'phone' => '98129 53730',
                'phone_verified_at' => now(),
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => 'user',
            ],
            [
                'name' => 'Raja',
                'email' => 'raja@example.com',
                'phone' => '88149 00944',
                'phone_verified_at' => now(),
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => 'user',
            ],
            [
                'name' => 'Vikas Bansal',
                'email' => 'vikas@example.com',
                'phone' => '9896840314',
                'phone_verified_at' => now(),
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => 'user',
            ],
            [
                'name' => 'Laddy',
                'email' => 'laddy@example.com',
                'phone' => '9468093507',
                'phone_verified_at' => now(),
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => 'user',
            ],
        ];

        foreach ($users as $user) {
            User::firstOrCreate(
                ['email' => $user['email']],
                $user
            );
        }
    }
}
