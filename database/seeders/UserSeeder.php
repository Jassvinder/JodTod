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
                'name' => 'Admin User',
                'email' => 'admin@jodtod.com',
                'phone' => '9999999999',
                'phone_verified_at' => now(),
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => 'admin',
            ],
            [
                'name' => 'Rahul Sharma',
                'email' => 'rahul@example.com',
                'phone' => '9876543210',
                'phone_verified_at' => now(),
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => 'user',
            ],
            [
                'name' => 'Priya Patel',
                'email' => 'priya@example.com',
                'phone' => '9876543211',
                'phone_verified_at' => now(),
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => 'user',
            ],
            [
                'name' => 'Amit Singh',
                'email' => 'amit@example.com',
                'phone' => '9876543212',
                'phone_verified_at' => now(),
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => 'user',
            ],
            [
                'name' => 'Neha Gupta',
                'email' => 'neha@example.com',
                'phone' => '9876543213',
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
